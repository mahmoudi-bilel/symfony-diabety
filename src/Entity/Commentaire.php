<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private ?string $textcommentaire = null;

    #[ORM\Column(length: 255)]
    private ?string $visible = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Publication $pub = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTextcommentaire(): ?string
    {
        return $this->textcommentaire;
    }

    public function setTextcommentaire(string $textcommentaire): self
    {
        $this->textcommentaire = $textcommentaire;

        return $this;
    }

    public function getVisible(): ?string
    {
        return $this->visible;
    }

    public function setVisible(string $visible): self
    {
        $this->visible = $visible;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPub(): ?Publication
    {
        return $this->pub;
    }

    public function setPub(?Publication $pub): self
    {
        $this->pub = $pub;

        return $this;
    }
}
