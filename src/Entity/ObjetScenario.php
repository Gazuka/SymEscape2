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

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Aide", mappedBy="objetScenario", cascade={"persist", "remove"})
     */
    private $aide;

    /**
     * Retourne le seul objet possible
     *
     * @return void
     */
    public function getObjet()
    {
        if($this->bombe != null)
        {
            return $this->bombe;
        }
        if($this->aide != null)
        {
            return $this->aide;
        }
    }

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

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAide(): ?Aide
    {
        return $this->aide;
    }

    public function setAide(?Aide $aide): self
    {
        $this->aide = $aide;

        // set (or unset) the owning side of the relation if necessary
        $newObjetScenario = null === $aide ? null : $this;
        if ($aide->getObjetScenario() !== $newObjetScenario) {
            $aide->setObjetScenario($newObjetScenario);
        }

        return $this;
    }
}
