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
     * @ORM\Column(type="text", nullable=true)
     */
    private $motif;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="signalements")
     */
    private $Produit;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="signalements")
     */
    private $Utilisateur;

    /**
     * @ORM\ManyToOne(targetEntity=Commentaires::class, inversedBy="signalements")
     */
    private $Commentaires;

    /**
     * @ORM\ManyToOne(targetEntity=Entreprise::class, inversedBy="signalements")
     */
    private $Entreprise;

    /**
     * @ORM\ManyToOne(targetEntity=Message::class, inversedBy="signalements")
     */
    private $Message;

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

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(?string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getProduit(): ?Produit
    {
        return $this->Produit;
    }

    public function setProduit(?Produit $Produit): self
    {
        $this->Produit = $Produit;

        return $this;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->Utilisateur;
    }

    public function setUtilisateur(?Utilisateur $Utilisateur): self
    {
        $this->Utilisateur = $Utilisateur;

        return $this;
    }

    public function getCommentaires(): ?Commentaires
    {
        return $this->Commentaires;
    }

    public function setCommentaires(?Commentaires $Commentaires): self
    {
        $this->Commentaires = $Commentaires;

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->Entreprise;
    }

    public function setEntreprise(?Entreprise $Entreprise): self
    {
        $this->Entreprise = $Entreprise;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->Message;
    }

    public function setMessage(?Message $Message): self
    {
        $this->Message = $Message;

        return $this;
    }
}
