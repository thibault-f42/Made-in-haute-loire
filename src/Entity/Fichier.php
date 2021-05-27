<?php

namespace App\Entity;

use App\Repository\FichierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FichierRepository::class)
 */
class Fichier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $urlFichier;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $typeFichier;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="fichier")
     */
    private $entreprise;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="fichier")
     */
    private $produit;

    public function __construct()
    {
        $this->entreprise = new ArrayCollection();
        $this->produit = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrlFichier(): ?string
    {
        return $this->urlFichier;
    }

    public function setUrlFichier(string $urlFichier): self
    {
        $this->urlFichier = $urlFichier;

        return $this;
    }


    public function getTypeFichier(): ?string
    {
        return $this->typeFichier;
    }

    public function setTypeFichier(string $typeFichier): self
    {
        $this->typeFichier = $typeFichier;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }
}
