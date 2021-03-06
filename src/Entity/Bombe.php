<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BombeRepository")
 */
class Bombe
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $start;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Fil", mappedBy="bombe", cascade={"persist", "remove"})
     */
    private $fils;

    /**
     * @ORM\Column(type="boolean")
     */
    private $pince;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ObjetScenario", mappedBy="bombe", cascade={"persist", "remove"})
     */
    private $objetScenario;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $dureeFin;

    /**
     * @ORM\OneToMany(targetEntity=Boulon::class, mappedBy="bombe", cascade={"persist", "remove"})
     */
    private $boulons;

    public function __construct()
    {
        $this->fils = new ArrayCollection();
        $this->boulons = new ArrayCollection();
        $this->boulons = new ArrayCollection();
    }

    public function FilsRestants()
    {
        $filsRestants = array();
        foreach($this->fils as $fil)
        {
            if($fil->getEtat() == true)
            {
                array_push($filsRestants, $fil);
            }
        }
        return $filsRestants;
    }
    
    private function NbrFil($fils, $couleur)
    {
        $nbr = 0;
        foreach($fils as $fil)
        {
            if($fil->getCouleur() == $couleur)
            {
                $nbr = $nbr + 1;
                //dump($fil);
                //dump($couleur.": ".$nbr);
            }
        }
        return $nbr;
    }

    public function BoulonsRestantes()
    {
        $nbrBoulons = 0;
        foreach($this->boulons as $boulons)
        {
            if($boulons->getEtat() == true)
            {
                $nbrBoulons = $nbrBoulons + 1;
            }
        }
        return $nbrBoulons;
    }

    public function Devisser()
    {
        if($this->BoulonsRestantes() > 0)
        {
            foreach($this->boulons as $boulons)
            {
                if($boulons->getEtat() == true)
                {
                    $boulons->setEtat(false);
                    break;
                }
            }
        }
    }

    public function filACouper()
    {
        $fils = $this->FilsRestants();
        //dump($fils);
        $nbrFilsRouges = $this->NbrFil($fils, 'rouge');
        $nbrFilsBlancs = $this->NbrFil($fils, 'blanc');
        $nbrFilsBleus = $this->NbrFil($fils, 'bleu');
        $nbrFilsJaunes = $this->NbrFil($fils, 'jaune');
        $nbrFilsNoirs = $this->NbrFil($fils, 'noir');
        /*dump($nbrFilsRouges);
        dump($nbrFilsBlancs);
        dump($nbrFilsBleus);
        dump($nbrFilsJaunes);
        dump($nbrFilsNoirs);*/
        
        switch(sizeof($fils))
        {
            case '6':
                //S'il n'y a pas de fil jaune, coupez le troisième fil.
                if($nbrFilsJaunes == 0)
                {
                    $couper = 3;
                }
                else
                {
                    //Sinon, s'il y a exactement un fil jaune et s'il y a plus d'un fil blanc, coupez le quatrième fil.
                    if($nbrFilsJaunes == 1 && $nbrFilsBlancs > 1)
                    {
                        $couper = 4;
                    }
                    else
                    {
                        //Sinon, s'il n'y a pas de fil rouge, coupez le dernier fil.
                        if($nbrFilsRouges == 0)
                        {
                            $couper = 6;
                        }
                        else
                        {
                            //Sinon, coupez le quatrième fil.
                            $couper = 4;
                        }                        
                    }
                }
            break;

            case '5':
                //Si le dernier fil est noir, coupez le quatrième fil.
                if($fils[4]->getCouleur() == 'noir')
                {
                    $couper = 4;
                }
                else
                {
                    //Sinon, s'il y a exactement un fil rouge et s'il y a plus d'un fil jaune, coupez le premier fil.
                    if($nbrFilsRouges == 1 && $nbrFilsJaunes > 1)
                    {
                        $couper = 1;
                    }
                    else
                    {
                        //Sinon, s'il n'y a pas de fil noir, coupez le second fil.
                        if($nbrFilsNoirs == 0)
                        {
                            $couper = 2;
                        }
                        else
                        {
                            //Sinon, coupez le premier fil.
                            $couper = 1;
                        }
                    }
                }
            break;

            case '4':
                //S'il y a plus d'un fil rouge, coupez le dernier fil rouge.
                if($nbrFilsRouges > 1)
                {
                    for($i=4 ; $i>=1 ; $i--)
                    {
                        if($fils[$i-1]->getCouleur() == 'rouge')
                        {
                            $couper = $i;
                            $i=0;
                        }
                    }
                }
                else
                {
                    //Sinon, si le dernier fil est jaune et qu'il n'y a pas de fil rouge, coupez le premier fil.
                    if($fils[4-1]->getCouleur() == 'jaune' && $nbrFilsRouges == 0)
                    {
                        $couper = 1;
                    }
                    else
                    {
                        //Sinon, s'il y a exactement un fil bleu, coupez le premier fil.
                        if($nbrFilsBleus == 1)
                        {
                            $couper = 1;
                        }
                        else
                        {
                            //Sinon, s'il y a plus d'un fil jaune, coupez le dernier fil.
                            if($nbrFilsJaunes > 1)
                            {
                                $couper = 4;
                            }
                            else
                            {
                                //Sinon, coupez le deuxième fil.
                                $couper = 2;
                            }
                        }
                    }
                }
            break;

            case '3':
                //S'il n'y a pas de fil rouge, coupez le deuxième fil.
                if($nbrFilsRouges == 0)
                {                    
                    $couper = 2;
                }
                else
                {
                    //Sinon, si le dernier fil est blanc, coupez le dernier fil.
                    if($fils[3-1]->getCouleur() == 'blanc')
                    {
                        $couper = 3;
                    }
                    else
                    {
                        //Sinon, s'il y a plus d'un fil bleu, coupez le dernier fil bleu. 
                        if($nbrFilsBleus > 1)
                        {
                            for($i=3 ; $i>=1 ; $i--)
                            {
                                if($fils[$i-1]->getCouleur() == 'bleu')
                                {
                                    $couper = $i;
                                    $i=0;
                                }
                            }
                        }
                        else
                        {
                            //Sinon, coupez le dernier fil.
                            $couper = 3;
                        }
                    }
                }
            break;

            case '2':
                //Si les deux fils sont identique, coupez le …………………….
                //peu importe
                return null;
            break;
        }
        return $fils[$couper-1]->getId();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return Collection|Fil[]
     */
    public function getFils(): Collection
    {
        return $this->fils;
    }

    public function addFil(Fil $fil): self
    {
        if (!$this->fils->contains($fil)) {
            $this->fils[] = $fil;
            $fil->setBombe($this);
        }

        return $this;
    }

    public function removeFil(Fil $fil): self
    {
        if ($this->fils->contains($fil)) {
            $this->fils->removeElement($fil);
            // set the owning side to null (unless already changed)
            if ($fil->getBombe() === $this) {
                $fil->setBombe(null);
            }
        }

        return $this;
    }

    public function getPince(): ?bool
    {
        return $this->pince;
    }

    public function setPince(bool $pince): self
    {
        $this->pince = $pince;

        return $this;
    }

    public function __toString(): string
    {
        return "bombe_".$this->id;
    }

    public function getObjetScenario(): ?ObjetScenario
    {
        return $this->objetScenario;
    }

    public function setObjetScenario(?ObjetScenario $objetScenario): self
    {
        $this->objetScenario = $objetScenario;

        // set (or unset) the owning side of the relation if necessary
        $newBombe = null === $objetScenario ? null : $this;
        if ($objetScenario->getBombe() !== $newBombe) {
            $objetScenario->setBombe($newBombe);
        }

        return $this;
    }

    /**
     * Permet de démarrer la bombe
     *
     * @return void
     */
    public function StartBombe()
    {
        $now = new DateTime(); 
        $now->format('Y-m-d H:i:s'); 
        $this->start = $now; 
    }

    /**
     * Donne en secondes la durée restante sur la bombe
     */
    public function calculDureeBombe(): int
    {
        //Nombres de secondes entre maintenant et l'heure de début de la bombe
        $now = new DateTime();
        $difference = $this->start->diff($now);
        $DureeEcoule = $difference->format('%h') * 60 * 60 + $difference->format('%i') * 60 + $difference->format('%s');
        $DureeDeLaBombe = $this->duration;
        $DureeRestante = $DureeDeLaBombe - $DureeEcoule;        
        return $DureeRestante;
    }

    public function getDureeFin(): ?int
    {
        return $this->dureeFin;
    }

    public function setDureeFin(?int $dureeFin): self
    {
        $this->dureeFin = $dureeFin;

        return $this;
    }

    /**
     * @return Collection|Boulon[]
     */
    public function getBoulons(): Collection
    {
        return $this->boulons;
    }

    public function addBoulon(Boulon $boulon): self
    {
        if (!$this->boulons->contains($boulon)) {
            $this->boulons[] = $boulon;
            $boulon->setBombe($this);
        }

        return $this;
    }

    public function removeBoulon(Boulon $boulon): self
    {
        if ($this->boulons->contains($boulon)) {
            $this->boulons->removeElement($boulon);
            // set the owning side to null (unless already changed)
            if ($boulon->getBombe() === $this) {
                $boulon->setBombe(null);
            }
        }

        return $this;
    }
}
