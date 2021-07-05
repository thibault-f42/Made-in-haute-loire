<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
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
    private $nomArticle;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $etatVente;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

     /**
      * @ORM\Column(type="string", length=255)
     */
    private $codeProduit;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="produits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $entreprise;


    /**
     * @ORM\OneToMany(targetEntity=Fichier::class, mappedBy="produit",  cascade={"persist"})
     */
    private $fichiers;

    /**
     * @ORM\ManyToOne(targetEntity=SousCategorie::class, inversedBy="produits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sousCategorie;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="produit")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categorie;

    /**
     * @ORM\ManyToMany(targetEntity=Commande::class, mappedBy="produit")
     */
    private $commandes;

    /**
     * @ORM\OneToMany(targetEntity=SousCommande::class, mappedBy="produit", orphanRemoval=true)
     */
    private $sousCommandes;



    

    public function __construct()
    {
        $this->fichiers = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->sousCommandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomArticle(): ?string
    {
        return $this->nomArticle;
    }

    public function setNomArticle(string $nomArticle): self
    {
        $this->nomArticle = $nomArticle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getEtatVente(): ?String
    {
        return $this->etatVente;
    }

    public function setEtatVente(String $etatVente): self
    {
        $this->etatVente = $etatVente;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getCodeProduit(): ?String
    {
        return $this->codeProduit;
    }

    public function setCodeProduit(String $codeProduit): self
    {
        $this->codeProduit = $codeProduit;

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


    /**
     * @return Collection|Fichier[]
     */
    public function getFichiers(): Collection
    {
        return $this->fichiers;
    }

    public function addFichier(Fichier $fichier): self
    {
        if (!$this->fichiers->contains($fichier)) {
            $this->fichiers[] = $fichier;
            $fichier->setProduit($this);
        }

        return $this;
    }

    public function removeFichier(Fichier $fichier): self
    {
        if ($this->fichiers->removeElement($fichier)) {
            // set the owning side to null (unless already changed)
            if ($fichier->getProduit() === $this) {
                $fichier->setProduit(null);
            }
        }

        return $this;
    }

    public function getSousCategorie(): ?SousCategorie
    {
        return $this->sousCategorie;
    }

    public function setSousCategorie(?SousCategorie $sousCategorie): self
    {
        $this->sousCategorie = $sousCategorie;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->categorie;
    }

    public function setCategorie(?Categorie $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->addProduit($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            $commande->removeProduit($this);
        }

        return $this;
    }

    /**
     * @return Collection|SousCommande[]
     */
    public function getSousCommandes(): Collection
    {
        return $this->sousCommandes;
    }

    public function addSousCommande(SousCommande $sousCommande): self
    {
        if (!$this->sousCommandes->contains($sousCommande)) {
            $this->sousCommandes[] = $sousCommande;
            $sousCommande->setProduit($this);
        }

        return $this;
    }

    public function removeSousCommande(SousCommande $sousCommande): self
    {
        if ($this->sousCommandes->removeElement($sousCommande)) {
            // set the owning side to null (unless already changed)
            if ($sousCommande->getProduit() === $this) {
                $sousCommande->setProduit(null);
            }
        }

        return $this;
    }


   
}
