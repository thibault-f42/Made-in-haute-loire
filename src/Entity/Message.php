<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Utilisateur::class, inversedBy="messages")
     */
    private $utilisateur;

    /**
     * @ORM\Column(type="text")
     */
    private $corps;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=Conversation::class, inversedBy="messages")
     * @ORM\JoinColumn(nullable=false)
     */
    private $conversarion;

    /**
     * @ORM\OneToOne(targetEntity=Conversation::class, mappedBy="lastMessage", cascade={"persist", "remove"})
     */
    private $lastMessageInConversation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUtilisateur(): ?Utilisateur
    {
        return $this->utilisateur;
    }

    public function setUtilisateur(?Utilisateur $utilisateur): self
    {
        $this->utilisateur = $utilisateur;

        return $this;
    }

    public function getCorps(): ?string
    {
        return $this->corps;
    }

    public function setCorps(string $corps): self
    {
        $this->corps = $corps;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getConversarion(): ?Conversation
    {
        return $this->conversarion;
    }

    public function setConversarion(?Conversation $conversarion): self
    {
        $this->conversarion = $conversarion;

        return $this;
    }

    public function getLastMessageInConversation(): ?Conversation
    {
        return $this->lastMessageInConversation;
    }

    public function setLastMessageInConversation(?Conversation $lastMessageInConversation): self
    {
        // unset the owning side of the relation if necessary
        if ($lastMessageInConversation === null && $this->lastMessageInConversation !== null) {
            $this->lastMessageInConversation->setLastMessage(null);
        }

        // set the owning side of the relation if necessary
        if ($lastMessageInConversation !== null && $lastMessageInConversation->getLastMessage() !== $this) {
            $lastMessageInConversation->setLastMessage($this);
        }

        $this->lastMessageInConversation = $lastMessageInConversation;

        return $this;
    }
}
