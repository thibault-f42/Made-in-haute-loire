<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\QueryBuilder;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 */
class Ville
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
    private $nom;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $codePostal;

    /**
     * @ORM\OneToMany(targetEntity=Utilisateur::class, mappedBy="ville")
     */
    private $utilisateurs;


    /**
     * @ORM\ManyToOne(targetEntity=Canton::class, inversedBy="villes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $canton;

    /**
     * @ORM\Column(type="float")
     */
    private $longitude;

    /**
     * @ORM\Column(type="float")
     */
    private $latitude;

    /**
     * @ORM\OneToMany(targetEntity=Entreprise::class, mappedBy="ville")
     */
    private $entreprises;

    /**
     * @ORM\OneToMany(targetEntity=AdresseLivraison::class, mappedBy="ville")
     */
    private $adresseLivraison;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->entreprises = new ArrayCollection();
        $this->adresseLivraison = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    public function setNo(Utilisateur $no): self
    {
        // set the owning side of the relation if necessary
        if ($no->getVille() !== $this) {
            $no->setVille($this);
        }

        $this->no = $no;

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
            $utilisateur->setVille($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateurs->removeElement($utilisateur)) {
            // set the owning side to null (unless already changed)
            if ($utilisateur->getVille() === $this) {
                $utilisateur->setVille(null);
            }
        }

        return $this;
    }



    public function getCanton(): ?Canton
    {
        return $this->canton;
    }

    public function setCanton(?Canton $canton): self
    {
        $this->canton = $canton;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(int $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(int $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * @return Collection|Entreprise[]
     */
    public function getEntreprises(): Collection
    {
        return $this->entreprises;
    }

    public function addEntreprise(Entreprise $entreprise): self
    {
        if (!$this->entreprises->contains($entreprise)) {
            $this->entreprises[] = $entreprise;
            $entreprise->setVille($this);
        }

        return $this;
    }

    public function removeEntreprise(Entreprise $entreprise): self
    {
        if ($this->entreprises->removeElement($entreprise)) {
            // set the owning side to null (unless already changed)
            if ($entreprise->getVille() === $this) {
                $entreprise->setVille(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AdresseLivraison[]
     */
    public function getAdresseLivraison(): Collection
    {
        return $this->adresseLivraison;
    }

    public function addAdresse(AdresseLivraison $adresseLivraison): self
    {
        if (!$this->adresseLivraison->contains($adresseLivraison)) {
            $this->adresseLivraison[] = $adresseLivraison;
            $adresseLivraison->setVille($this);
        }

        return $this;
    }

    public function removeAdresseLivraison(AdresseLivraison $adresseLivraison): self
    {
        if ($this->adresseLivraison->removeElement($adresseLivraison)) {
            // set the owning side to null (unless already changed)
            if ($adresseLivraison->getVille() === $this) {
                $adresseLivraison->setVille(null);
            }
        }

        return $this;
    }


}
