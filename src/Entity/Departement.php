<?php

namespace App\Entity;

use App\Repository\DepartementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=DepartementRepository::class)
 * @UniqueEntity(fields={"$noDepartement"}, message="Ce numéro de département est déjà attribué ")
 * @UniqueEntity(fields={"nom"}, message="Ce département existe déjà ")
 */
class Departement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $noDepartement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Canton::class, mappedBy="departement")
     */
    private $cantons;


    /**
     * @ORM\ManyToOne(targetEntity=Region::class, inversedBy="departements")
     */
    private $region;


    public function __construct()
    {
        $this->cantons = new ArrayCollection();
        $this->villes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNoDepartement(): ?int
    {
        return $this->noDepartement;
    }

    public function setNoDepartement(int $noDepartement): self
    {
        $this->noDepartement = $noDepartement;

        return $this;
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

    /**
     * @return Collection|Canton[]
     */
    public function getCantons(): Collection
    {
        return $this->cantons;
    }

    public function addCanton(Canton $canton): self
    {
        if (!$this->cantons->contains($canton)) {
            $this->cantons[] = $canton;
            $canton->setDepartement($this);
        }

        return $this;
    }

    public function removeCanton(Canton $canton): self
    {
        if ($this->cantons->removeElement($canton)) {
            // set the owning side to null (unless already changed)
            if ($canton->getDepartement() === $this) {
                $canton->setDepartement(null);
            }
        }

        return $this;
    }

    public function getRelation(): ?string
    {
        return $this->relation;
    }

    public function setRelation(string $relation): self
    {
        $this->relation = $relation;

        return $this;
    }

    public function getRegion(): ?Region
    {
        return $this->region;
    }

    public function setRegion(?Region $region): self
    {
        $this->region = $region;

        return $this;
    }
}
