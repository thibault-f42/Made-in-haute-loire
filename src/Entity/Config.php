<?php

namespace App\Entity;

use App\Repository\ConfigRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConfigRepository::class)
 */
class Config
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clePubliqueStripe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $clePriveeStripe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClePubliqueStripe(): ?string
    {
        return $this->clePubliqueStripe;
    }

    public function setClePubliqueStripe(?string $clePubliqueStripe): self
    {
        $this->clePubliqueStripe = $clePubliqueStripe;

        return $this;
    }

    public function getClePriveeStripe(): ?string
    {
        return $this->clePriveeStripe;
    }

    public function setClePriveeStripe(?string $clePriveeStripe): self
    {
        $this->clePriveeStripe = $clePriveeStripe;

        return $this;
    }
}
