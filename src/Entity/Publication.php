<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;




#[ORM\Entity(repositoryClass: PublicationRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse e-mail est déjà utilisée.')]

class Publication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups("publication")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[Assert\Regex(pattern: '/^[a-zA-Z0-9]+$/')]
    #[Groups("publication")]
    private ?string $titre = null;
    #[Assert\NotBlank]
    #[Assert\Length(min: 10, max: 255)]
    #[ORM\Column(length: 255)]
    #[Groups("publication")]

    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("publication")]
    private ?\DateTimeInterface $createdAT = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Groups("publication")]
    private ?\DateTimeInterface $updatedAT = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Email]
    #[Groups("publication")]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\+\d{11}$/',
        message: 'Le numéro de téléphone doit être composé de 11 chiffres et commencer par un signe plus (+).'
    )]
    #[Groups("publication")]
    private ?string $numerodetel = null;

    #[ORM\Column(nullable: true)]
    #[Groups("publication")]
    private ?int $likes = null;

    #[ORM\Column(nullable: true)]
    #[Groups("publication")]
    private ?int $dislikes = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCreatedAT(): ?\DateTimeInterface
    {
        return $this->createdAT;
    }

    public function setCreatedAT(?\DateTimeInterface $createdAT): self
    {
        $this->createdAT = $createdAT;

        return $this;
    }

    public function getUpdatedAT(): ?\DateTimeInterface
    {
        return $this->updatedAT;
    }

    public function setUpdatedAT(?\DateTimeInterface $updatedAT): self
    {
        $this->updatedAT = $updatedAT;

        return $this;
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

    public function __toString()
{

    return $this->description . ' (' . $this->createdAT->format('Y-m-d H:i:s') . ')';
}

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getNumerodetel(): ?string
    {
        return $this->numerodetel;
    }

    public function setNumerodetel(string $numerodetel): self
    {
        $this->numerodetel = $numerodetel;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(?int $likes): self
    {
        $this->likes = $likes;

        return $this;
    }

    public function getDislikes(): ?int
    {
        return $this->dislikes;
    }

    public function setDislikes(?int $dislikes): self
    {
        $this->dislikes = $dislikes;

        return $this;
    }


}
