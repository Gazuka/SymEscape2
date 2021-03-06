<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EtapeRepository")
 */
class Etape
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Scenario", inversedBy="etapes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $scenario;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Etape", inversedBy="enfants")
     */
    private $parents;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Etape", mappedBy="parents")
     */
    private $enfants;

    /**
     * @ORM\Column(type="text")
     */
    private $descriptif;

    /**
     * @ORM\Column(type="boolean")
     */
    private $automatique;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Commut", mappedBy="etape", orphanRemoval=true)
     */
    private $commuts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Indice", mappedBy="etape", orphanRemoval=true, cascade={"persist", "remove"})
     */
    private $indices;

    public function __construct()
    {
        $this->parents = new ArrayCollection();
        $this->enfants = new ArrayCollection();
        $this->commuts = new ArrayCollection();
        $this->indices = new ArrayCollection();
        for($i=0;$i<3;$i++)
        {
            $indice = new Indice();
            $indice->setDescriptif('...');
            $this->addIndice($indice);
        }
    }

    public function __toString()
    {
        return $this->titre;
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

    public function setDescriptif(string $descriptif): self
    {
        $this->descriptif = $descriptif;

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
            $commut->setEtape($this);
        }

        return $this;
    }

    public function removeCommut(Commut $commut): self
    {
        if ($this->commuts->contains($commut)) {
            $this->commuts->removeElement($commut);
            // set the owning side to null (unless already changed)
            if ($commut->getEtape() === $this) {
                $commut->setEtape(null);
            }
        }

        return $this;
    }

    /**
     * Retourne true si tous les parents sont activés
     *
     * @return void
     */
    public function verifParents()
    {
        $deblocable = true;
        foreach($this->parents as $parent)
        {
            if($parent->getEtat == false)
            {
                $deblocable == false;
            }
        }
        return $deblocable;
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
            $indice->setEtape($this);
        }

        return $this;
    }

    public function removeIndice(Indice $indice): self
    {
        if ($this->indices->contains($indice)) {
            $this->indices->removeElement($indice);
            // set the owning side to null (unless already changed)
            if ($indice->getEtape() === $this) {
                $indice->setEtape(null);
            }
        }

        return $this;
    }
}
