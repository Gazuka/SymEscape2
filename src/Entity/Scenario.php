<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ScenarioRepository")
 */
class Scenario
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="scenario")
     */
    private $games;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Etape", mappedBy="scenario", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $etapes;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->etapes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setScenario($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->contains($game)) {
            $this->games->removeElement($game);
            // set the owning side to null (unless already changed)
            if ($game->getScenario() === $this) {
                $game->setScenario(null);
            }
        }

        return $this;
    }

    /*
    public function NextTriggs()
    {
        $next = Array();
        foreach($this->triggs as $trigg)
        {
            if($trigg->getDeblocable() == true)
            {
                array_push($next, $trigg);
            }
        }
        return $next;
    }*/

    /**
     * @return Collection|Etape[]
     */
    public function getEtapes(): Collection
    {
        return $this->etapes;
    }

    public function addEtape(Etape $etape): self
    {
        if (!$this->etapes->contains($etape)) {
            $this->etapes[] = $etape;
            $etape->setScenario($this);
        }

        return $this;
    }

    public function removeEtape(Etape $etape): self
    {
        if ($this->etapes->contains($etape)) {
            $this->etapes->removeElement($etape);
            // set the owning side to null (unless already changed)
            if ($etape->getScenario() === $this) {
                $etape->setScenario(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->titre;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
