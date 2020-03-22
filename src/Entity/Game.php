<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
 */
class Game
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
     * @ORM\OneToMany(targetEntity="App\Entity\Joueur", mappedBy="game", cascade={"persist", "remove"})
     */
    private $joueurs;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Scenario", inversedBy="game", cascade={"persist", "remove"})
     */
    private $scenario;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commut", mappedBy="partie", orphanRemoval=true)
     */
    private $commuts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ObjetScenario", mappedBy="game", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $objetsScenario;

    public function __construct()
    {
        $this->joueurs = new ArrayCollection();
        $this->commuts = new ArrayCollection();
        $this->objetsScenario = new ArrayCollection();
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

    /**
     * @return Collection|Joueur[]
     */
    public function getJoueurs(): Collection
    {
        return $this->joueurs;
    }

    public function addJoueur(Joueur $joueur): self
    {
        if (!$this->joueurs->contains($joueur)) {
            $this->joueurs[] = $joueur;
            $joueur->setGame($this);
        }

        return $this;
    }

    public function removeJoueur(Joueur $joueur): self
    {
        if ($this->joueurs->contains($joueur)) {
            $this->joueurs->removeElement($joueur);
            // set the owning side to null (unless already changed)
            if ($joueur->getGame() === $this) {
                $joueur->setGame(null);
            }
        }

        return $this;
    }

    /**
     * Donne en secondes la durée depuis le début de la partie
     */
    public function calculDureeDeJeu(): int
    {
        //Nombres de secondes entre maintenant et l'heure de début
        if($this->getStart() != null)
        {
            $now = new DateTime();
            $difference = $this->getStart()->diff($now);
            $DureeDeJeu = $difference->format('%h') * 60 * 60 + $difference->format('%i') * 60 + $difference->format('%s');
        }
        else
        {
            $DureeDeJeu = 0;
        }
        return $DureeDeJeu;
    }

    /**
     * Donne en secondes la durée restante sur la bombe
     */
    public function calculDureeBombe(): int
    {
        //Nombres de secondes entre maintenant et l'heure de début de la bombe
        $now = new DateTime();
        $difference = $this->getBombe()->getStart()->diff($now);
        $DureeEcoule = $difference->format('%h') * 60 * 60 + $difference->format('%i') * 60 + $difference->format('%s');
        $DureeDeLaBombe = $this->getBombe()->getDuration();
        $DureeRestante = $DureeDeLaBombe - $DureeEcoule;        
        return $DureeRestante;
    }

    /**
     * Met en route la partie 
     */
    public function Debuter(): bool
    {
        if($this->start == null)
        {
            $now = new DateTime();
            $now->format('Y-m-d H:i:s');
            $this->setStart($now);                  
            return true;
        }
        return false;
    }

    /**
     * Active la bombe 
     */
    public function StartBombe()
    {
        $now = new DateTime();
        $now->format('Y-m-d H:i:s');
        //$this->getBombe()->setStart($now);// A retoucher
    }

    public function getScenario(): ?Scenario
    {
        return $this->scenario;
    }

    public function setScenario(?Scenario $scenario): self
    {
        $this->scenario = $scenario;

        return $this;
    }

    /**
     * @return Collection|Commut[]
     */
    public function getCommuts(): Collection
    {
        return $this->commuts;
    }

    public function addCommut(Commut $commut): self
    {
        if (!$this->commuts->contains($commut)) {
            $this->commuts[] = $commut;
            $commut->setGame($this);
        }

        return $this;
    }

    public function removeCommut(Commut $commut): self
    {
        if ($this->commuts->contains($commut)) {
            $this->commuts->removeElement($commut);
            // set the owning side to null (unless already changed)
            if ($commut->getGame() === $this) {
                $commut->setGame(null);
            }
        }

        return $this;
    }

    public function getObjetsScenario(): Collection
    {
        return $this->objetsScenario;
    }

    public function addObjetsScenario(ObjetScenario $objetsScenario): self
    {
        if (!$this->objetsScenario->contains($objetsScenario)) {
            $this->objetsScenario[] = $objetsScenario;
            $objetsScenario->setGame($this);
        }

        return $this;
    }

    public function removeObjetsScenario(ObjetScenario $objetsScenario): self
    {
        if ($this->objetsScenario->contains($objetsScenario)) {
            $this->objetsScenario->removeElement($objetsScenario);
            // set the owning side to null (unless already changed)
            if ($objetsScenario->getGame() === $this) {
                $objetsScenario->setGame(null);
            }
        }

        return $this;
    }
}
