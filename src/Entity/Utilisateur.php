<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 * @UniqueEntity(fields={"email"}, message="Cette adresse mail est déjà utilisée.")
 * @UniqueEntity(fields={"telephone"}, message="Ce numéro de téléphone existe déjà. Veuillez vous connecter à votre compte ou en sélectionnez un autre numéro.")
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;

//    Pour le premier utilisateur, passe le role en : ROLE_ADMIN
    /**
     * @ORM\Column(type="json")
     *
     */
    private $roles = ['ROLE_USER'];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Regex("/^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$/")
     */
    private $telephone;

    /**
     * @ORM\Column(type="boolean")
     */
    private $vendeur;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\ManyToOne(targetEntity=Ville::class, inversedBy="utilisateurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ville;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\OneToOne(targetEntity=Entreprise::class, inversedBy="utilisateur", cascade={"persist", "remove"})
     */
    private $entreprise;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="utilisateur")
     */
    private $commandes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $activationToken;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tokenMDP;

    /**
     * @ORM\ManyToOne(targetEntity=AdresseLivraison::class, inversedBy="utilisateurs")
     */
    private $adresseLivraison;

    /**
     * @ORM\OneToMany(targetEntity=SousCommande::class, mappedBy="Utilisateur", orphanRemoval=true)
     */
    private $sousCommandes;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $Actif;

    /**
     * @ORM\ManyToMany(targetEntity=Conversation::class, mappedBy="user")
     * @ORM\OrderBy({"lastMessage" = "DESC"})
     */
    private $conversations;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="utilisateur")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity=Signalement::class, mappedBy="Utilisateur")
     */
    private $signalements;


    public function __construct()
    {
        $this->commandes = new ArrayCollection();
        $this->sousCommandes = new ArrayCollection();
        $this->conversations = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->signalements = new ArrayCollection();
    }

    public function isAuthorProduit (Produit $produit){
        if ($this == $produit->getEntreprise()->getUtilisateur()){
            return true;
        }
        return false;
    }

    public function getid(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getVendeur(): ?bool
    {
        return $this->vendeur;
    }

    public function setVendeur(bool $vendeur): self
    {
        $this->vendeur = $vendeur;

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

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }
    public function __toString()
    {
        return $this->nom;
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
            $commande->setUtilisateur($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUtilisateur() === $this) {
                $commande->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getActivationToken(): ?string
    {
        return $this->activationToken;
    }

    public function setActivationToken(?string $activationToken): self
    {
        $this->activationToken = $activationToken;

        return $this;
    }

    public function getTokenMDP(): ?string
    {
        return $this->tokenMDP;
    }

    public function setTokenMDP(?string $tokenMDP): self
    {
        $this->tokenMDP = $tokenMDP;

        return $this;
    }

    public function getAdresseLivraison(): ?AdresseLivraison
    {
        return $this->adresseLivraison;
    }

    public function setAdresseLivraison(?AdresseLivraison $adresseLivraison): self
    {
        $this->adresseLivraison = $adresseLivraison;

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
            $sousCommande->setUtilisateur($this);
        }

        return $this;
    }

    public function removeSousCommande(SousCommande $sousCommande): self
    {
        if ($this->sousCommandes->removeElement($sousCommande)) {
            // set the owning side to null (unless already changed)
            if ($sousCommande->getUtilisateur() === $this) {
                $sousCommande->setUtilisateur(null);
            }
        }

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->Actif;
    }

    public function setActif(?bool $Actif): self
    {
        $this->Actif = $Actif;

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */


    public function getConversations(): Collection
    {
        return $this->conversations;
    }

    public function addConversation(Conversation $conversation): self
    {
        if (!$this->conversations->contains($conversation)) {
            $this->conversations[] = $conversation;
            $conversation->addUser($this);
        }

        return $this;
    }

    public function removeConversation(Conversation $conversation): self
    {
        if ($this->conversations->removeElement($conversation)) {
            $conversation->removeUser($this);
        }

        return $this;
    }
    /**
     * @return array
     */
    public function findConversationByParticipants(Utilisateur $otherUser )
    {
        $conversations = null;
        foreach ($this->getConversations() as $conversationUser){
            foreach ($otherUser->getConversations() as $conversationOtherUser){
                if ($conversationUser === $conversationOtherUser){
                    $conversation = $conversationUser;
                    $conversations[] = $conversation;
                }
            }
        }
        return $conversations;
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
            $message->setUtilisateur($this);
        }
        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getUtilisateur() === $this) {
                $message->setUtilisateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Signalement>
     */
    public function getSignalements(): Collection
    {
        return $this->signalements;
    }

    public function addSignalement(Signalement $signalement): self
    {
        if (!$this->signalements->contains($signalement)) {
            $this->signalements[] = $signalement;
            $signalement->setUtilisateur($this);
        }

        return $this;
    }

    public function removeSignalement(Signalement $signalement): self
    {
        if ($this->signalements->removeElement($signalement)) {
            // set the owning side to null (unless already changed)
            if ($signalement->getUtilisateur() === $this) {
                $signalement->setUtilisateur(null);
            }
        }

        return $this;
    }

}
