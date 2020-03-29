<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IndiceRepository")
 */
class Indice
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etape", inversedBy="indices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etape;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptif;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(?string $descriptif): self
    {
        $this->descriptif = $descriptif;

        return $this;
    }
}