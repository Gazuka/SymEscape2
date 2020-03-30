<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AideRepository")
 */
class Aide
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ObjetScenario", inversedBy="aide", cascade={"persist", "remove"})
     */
    private $objetScenario;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Indice")
     */
    private $indices;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $demandeurs = [];

    private $message;

    public function __construct()
    {
        $this->indices = new ArrayCollection();
        $this->demandeurs = array();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjetScenario(): ?ObjetScenario
    {
        return $this->objetScenario;
    }

    public function setObjetScenario(?ObjetScenario $objetScenario): self
    {
        $this->objetScenario = $objetScenario;

        return $this;
    }

    /**
     * @return Collection|Indice[]
     */
    public function getIndices(): Collection
    {
        return $this->indices;
    }

    public function addIndice(Indice $indice): self
    {
        if (!$this->indices->contains($indice)) {
            $this->indices[] = $indice;
        }

        return $this;
    }

    public function removeIndice(Indice $indice): self
    {
        if ($this->indices->contains($indice)) {
            $this->indices->removeElement($indice);
        }

        return $this;
    }

    public function getDemandeurs(): ?array
    {
        return $this->demandeurs;
    }

    public function setDemandeurs(?array $demandeurs): self
    {
        $this->demandeurs = $demandeurs;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function demanderIndice($codeBarre)
    {
        //Si les joueurs n'ont jamais demandé d'indice on leur explique le principe
        if($this->objetScenario->getGame()->etatCommut('InfoIndice') == false)
        {
            $this->message = "A partir de maintenant, à chaque fois qu'un candidat scannera son badge d'accès, vous recevrez un indice. Mais attention, vous en subirez les conséquences...";
            $this->objetScenario->getGame()->onCommut('InfoIndice');

        }
        else
        {
             if($this->nbrIndicesEnCours() <= 4 )
            {
                //Si chaque joueur a déjà fait une demande, on remet les demandes à zéro
                if(sizeof($this->demandeurs) == sizeof($this->getObjetScenario()->getGame()->getJoueurs()))
                {
                    $this->demandeurs = Array();
                }
        
                //On vérifie si c'est la première demande du joueur
                $nouveauDemandeur = true;
                foreach($this->demandeurs as $demandeur)
                {
                    if($demandeur == $codeBarre)
                    {
                        $nouveauDemandeur = false;
                    }
                }
        
                //Si le joueur n'a pas encore fait de demande, on lui donne un indice
                if($nouveauDemandeur == true)
                {
                    //On enregistre que le joueur a fait une demande d'aide
                    array_push($this->demandeurs, $codeBarre);
                    //On verifie qui est le demandeur
                    foreach($this->getObjetScenario()->getGame()->getJoueurs() as $joueur)
                    {
                        if($joueur->getCodeBarre() == $codeBarre)
                        {
                            $this->message = $joueur->getPrenom()." a demandé un indice.";
                        }
                    }
                    //On récupère le bon indice
                    $this->recupererIndiceSuivant();
                }
                else
                {
                    //Sinon, on demande a un autre joueur de demander un indice
                    $this->message = "Vous avez déjà fait une demande d'indice, laissez un autre candidat le faire !";
                }
            }
            else
            {
                //Vous avez déjà 4 indices...
                $this->message = "Vous avez déjà quelques indices, utilisez-les !";
            }
        }
        return $this->message;
    }

    /**
     * Permet de récupérer le prochain indice disponible
     *
     * @return void
     */
    private function recupererIndiceSuivant()
    {
        //Aucun indice recu pour l'instant
        $indiceDonnee = false;
        //On récupére l'objet Game
        $game = $this->getObjetScenario()->getGame();
        //On récupére les commuts qui peuvent être débloqués donc qui réclament un indice
        $commutsDeblocables = $game->commutsDeblocables();

        foreach($commutsDeblocables as $commutDeblocable)
        {
            foreach($commutDeblocable->getEtape()->getIndices() as $indicePotentiel)
            {
                if($indicePotentiel->getDescriptif() != "...")
                {
                    //Tant qu'un indice n'est pas donné, on continu la recherche
                    if($indiceDonnee == false)
                    {
                        $indicePotentielDejaDonnee = false;
                        foreach($this->indices as $indicesDejaRecus)
                        {
                            if($indicesDejaRecus == $indicePotentiel)
                            {
                                //Cette indice est déjà divulgué
                                $indicePotentielDejaDonnee = true;
                            }
                        }
                        //Si l'indice potentiel n'a jamais été divulgué, on le donne aux joueurs
                        if($indicePotentielDejaDonnee == false)
                        {
                            //On donne l'indice
                            $this->addIndice($indicePotentiel);
                            //On indique qu'un indice est donnée
                            $indiceDonnee = true;
                        }
                    }
                }
            }
        }
        if($indiceDonnee == false)
        {
            //On indique que aucun indice n'est disponible pour le moment
            $this->message = "Aucun indice n'est disponible pour le moment...";
        }
    }

    private function nbrIndicesEnCours()
    {
        $nbrIndicesEnCours = 0;
        //On récupére l'objet Game
        $game = $this->getObjetScenario()->getGame();

        foreach($this->indices as $indice)
        {
            if($game->rechercheCommutEtape($indice->getEtape())->getEtat() == false)
            {
                $nbrIndicesEnCours = $nbrIndicesEnCours+1;
            }
        }
        return $nbrIndicesEnCours;
    }
}
