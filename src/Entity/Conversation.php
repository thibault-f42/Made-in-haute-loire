<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConversationRepository::class)
 */
class Conversation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Utilisateur::class, inversedBy="conversations")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="conversarion", orphanRemoval=true)
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $messages;

    /**
     * @ORM\OneToOne(targetEntity=Message::class, inversedBy="lastMessageInConversation", cascade={"persist", "remove"})
     * @ORM\OrderBy({"date" = "DESC"})
     */
    private $lastMessage;

    /**
     * @ORM\Column(type="integer", options={"default"=0})
     */
    private $nMessageNonVue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity=Produit::class, inversedBy="conversations")
     */
    private $produit;

    /**
     * @ORM\ManyToOne(targetEntity=SousCommande::class, inversedBy="conversations")
     */
    private $sousCommande;



    public function __construct()
    {
        $this->user = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->produit = new ArrayCollection();
        $this->commande = new ArrayCollection();
        $this->sousCommande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Utilisateur>
     */
    public function getUser(): Collection
    {
        return $this->user;
    }

    public function addUser(Utilisateur $user): self
    {
        if (!$this->user->contains($user)) {
            $this->user[] = $user;
        }

        return $this;
    }

    public function removeUser(Utilisateur $user): self
    {
        $this->user->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setConversarion($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getConversarion() === $this) {
                $message->setConversarion(null);
            }
        }

        return $this;
    }

    public function getLastMessage(): ?Message
    {
        return $this->lastMessage;
    }

    public function setLastMessage(?Message $lastMessage): self
    {
        $this->lastMessage = $lastMessage;

        return $this;
    }

    public function isParticipant(Utilisateur $utilisateur)
    {
        foreach ($this->getUser() as $participant){
            if ($participant === $utilisateur){
                return true ;
            }
        }
        return false;
    }

    public function getOtherUser(Utilisateur $utilisateur)
    {
        foreach ($this->getUser() as $user){
            if ($user != $utilisateur){
                return $user;
            }
        }
        return null;
    }

    public function getNMessageNonVue(): ?int
    {
        return $this->nMessageNonVue;
    }

    public function setNMessageNonVue(int $nMessageNonVue): self
    {
        $this->nMessageNonVue = $nMessageNonVue;

        return $this;
    }
    public function incrementNMessageNonVue(): self
    {
        $this->nMessageNonVue += 1;

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

    public function getProduit(): ArrayCollection
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getSousCommande(): ?SousCommande
    {
        return $this->sousCommande;
    }

    public function setSousCommande(?SousCommande $sousCommande): self
    {
        $this->sousCommande = $sousCommande;

        return $this;
    }

}
