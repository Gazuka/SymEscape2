<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TriggRepository")
 */
class Trigg
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Scenario", inversedBy="triggs")
     */
    private $scenario;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="boolean")
     */
    private $etat;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Trigg", inversedBy="enfants")
     */
    private $parents;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Trigg", mappedBy="parents")
     */
    private $enfants;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptif;

    /**
     * @ORM\Column(type="boolean")
     */
    private $deblocable;

    /**
     * @ORM\Column(type="boolean")
     */
    private $automatique;

    public function __construct()
    {
        $this->parents = new ArrayCollection();
        $this->enfants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getEtat(): ?bool
    {
        return $this->etat;
    }

    public function setEtat(bool $etat): self
    {
        $this->etat = $etat;
        //On change l'état en actif, on verifie si ca débloque des enfants
        if($etat==true)
        {
            foreach ($this->enfants as $enfant)
            {
                $enfant->verifParents();                
            }
        }

        return $this;
    }

    private function verifParents()
    {
        if($this->automatique == false)
        {
            $verif = true;
            foreach($this->getParents() as $parent)
            {
                if($parent->getEtat() == false)
                {
                    $verif = false;
                }
            }
            $this->deblocable = $verif;
        }
    }

    /**
     * @return Collection|self[]
     */
    public function getParents(): Collection
    {
        return $this->parents;
    }

    public function addParent(self $parent): self
    {
        if (!$this->parents->contains($parent)) {
            $this->parents[] = $parent;
        }

        return $this;
    }

    public function removeParent(self $parent): self
    {
        if ($this->parents->contains($parent)) {
            $this->parents->removeElement($parent);
        }

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getEnfants(): Collection
    {
        return $this->enfants;
    }

    public function addEnfant(self $enfant): self
    {
        if (!$this->enfants->contains($enfant)) {
            $this->enfants[] = $enfant;
            $enfant->addParent($this);
        }

        return $this;
    }

    public function removeEnfant(self $enfant): self
    {
        if ($this->enfants->contains($enfant)) {
            $this->enfants->removeElement($enfant);
            $enfant->removeParent($this);
        }

        return $this;
    }

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(?string $descriptif): self
    {
        $this->descriptif = $descriptif;

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

    public function getAutomatique(): ?bool
    {
        return $this->automatique;
    }

    public function setAutomatique(bool $automatique): self
    {
        $this->automatique = $automatique;

        return $this;
    }
}
