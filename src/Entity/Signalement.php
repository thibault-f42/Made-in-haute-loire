<?php

namespace App\Entity;

use App\Repository\SignalementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SignalementRepository::class)
 */
class Signalement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\OneToMany(targetEntity=message::class, mappedBy="signalement")
     */
    private $message;

    /**
     * @ORM\OneToMany(targetEntity=Entreprise::class, mappedBy="signalement")
     */
    private $Entreprise;

    /**
     * @ORM\OneToMany(targetEntity=Commentaires::class, mappedBy="signalement")
     */
    private $Commentaires;

    /**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="signalement")
     */
    private $Produit;

    /**
     * @ORM\OneToMany(targetEntity=Utilisateur::class, mappedBy="signalement")
     */
    private $Utilisateur;

    public function __construct()
    {
        $this->message = new ArrayCollection();
        $this->Entreprise = new ArrayCollection();
        $this->Commentaires = new ArrayCollection();
        $this->Produit = new ArrayCollection();
        $this->Utilisateur = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, message>
     */
    public function getMessage(): Collection
    {
        return $this->message;
    }

    public function addMessage(message $message): self
    {
        if (!$this->message->contains($message)) {
            $this->message[] = $message;
            $message->setSignalement($this);
        }

        return $this;
    }

    public function removeMessage(message $message): self
    {
        if ($this->message->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSignalement() === $this) {
                $message->setSignalement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Entreprise>
     */
    public function getEntreprise(): Collection
    {
        return $this->Entreprise;
    }

    public function addEntreprise(Entreprise $entreprise): self
    {
        if (!$this->Entreprise->contains($entreprise)) {
            $this->Entreprise[] = $entreprise;
            $entreprise->setSignalement($this);
        }

        return $this;
    }

    public function removeEntreprise(Entreprise $entreprise): self
    {
        if ($this->Entreprise->removeElement($entreprise)) {
            // set the owning side to null (unless already changed)
            if ($entreprise->getSignalement() === $this) {
                $entreprise->setSignalement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Commentaires>
     */
    public function getCommentaires(): Collection
    {
        return $this->Commentaires;
    }

    public function addCommentaire(Commentaires $commentaire): self
    {
        if (!$this->Commentaires->contains($commentaire)) {
            $this->Commentaires[] = $commentaire;
            $commentaire->setSignalement($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaires $commentaire): self
    {
        if ($this->Commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getSignalement() === $this) {
                $commentaire->setSignalement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduit(): Collection
    {
        return $this->Produit;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->Produit->contains($produit)) {
            $this->Produit[] = $produit;
            $produit->setSignalement($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->Produit->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getSignalement() === $this) {
                $produit->setSignalement(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUtilisateur(): Collection
    {
        return $this->Utilisateur;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->Utilisateur->contains($utilisateur)) {
            $this->Utilisateur[] = $utilisateur;
            $utilisateur->setSignalement($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->Utilisateur->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getSignalement() === $this) {
                $utilisateur->setSignalement(null);
            }
        }

        return $this;
    }
}
