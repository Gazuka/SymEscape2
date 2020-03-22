<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ObjetScenarioRepository")
 */
class ObjetScenario
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Bombe", inversedBy="objetScenario", cascade={"persist", "remove"})
     */
    private $bombe;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="ObjetsScenario")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBombe(): ?Bombe
    {
        return $this->bombe;
    }

    public function setBombe(?Bombe $bombe): self
    {
        $this->bombe = $bombe;

        return $this;
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

    public function __toString()
    {
        return "objet_".$this->id;
    }
}
