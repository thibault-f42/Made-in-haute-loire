<?php

namespace App\Entity;

use App\Repository\AdresseLivraisonRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdresseLivraisonRepository::class)
 */
class AdresseLivraison
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="adresseLivraison")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $InformationsImportantes;

    /**
     * @ORM\OneToMany(targetEntity=Utilisateur::class, mappedBy="adresseLivraison")
     */
    private $utilisateurs;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="adresseLivraison")
     */
    private $commandes;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->commandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getInformationsImportantes(): ?string
    {
        return $this->InformationsImportantes;
    }

    public function setInformationsImportantes(?string $InformationsImportantes): self
    {
        $this->InformationsImportantes = $InformationsImportantes;

        return $this;
    }

    /**
     * @return Collection|Utilisateur[]
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs[] = $utilisateur;
            $utilisateur->setAdresseLivraison($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getAdresseLivraison() === $this) {
                $utilisateur->setAdresseLivraison(null);
            }
        }

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
            $commande->setAdresseLivraison($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getAdresseLivraison() === $this) {
                $commande->setAdresseLivraison(null);
            }
        }

        return $this;
    }
}
