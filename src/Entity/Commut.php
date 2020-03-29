<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommutRepository")
 */
class Commut
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="commuts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etape", inversedBy="commuts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etape;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etat;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deblocable;

    /**
     * @ORM\Column(type="datetime")
     */
    private $horaireChangement;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getEtape(): ?Etape
    {
        return $this->etape;
    }

    public function setEtape(?Etape $etape): self
    {
        $this->etape = $etape;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;
        if($this->game != null)
        {
           //On demande à game de vérifier les changements de commuts résultant de cette modification (deblocable)
            $this->game->verifCommutsDeblocables($this); 
        } 
        //On note l'heure du changement d'état
        $now = new DateTime(); 
        $now->format('Y-m-d H:i:s'); 
        $this->horaireChangement = $now;
        
        return $this;
    }

    public function getDeblocable(): ?bool
    {
        return $this->deblocable;
    }

    public function setDeblocable(bool $deblocable): self
    {
        $this->deblocable = $deblocable;

        return $this;
    }

    public function verifSiDeblocable()
    {
        $deblocable = true;
        //Recherche les étapes Parents
        foreach($this->etape->getParents() as $parent)
        {
            //Recherche le Commut relié a l'étape Parente en cours
            if($this->game->rechercheCommutEtape($parent)->getEtat() == false)
            {
                $deblocable = false;
            }
        }
        $this->deblocable = $deblocable;
    }

    public function getHoraireChangement(): ?\DateTimeInterface
    {
        return $this->horaireChangement;
    }

    public function setHoraireChangement(\DateTimeInterface $horaireChangement): self
    {
        $this->horaireChangement = $horaireChangement;

        return $this;
    }
}
