<?php

namespace App\Entity;

use App\Config\OrientationEnum;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BoardRepository;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoardRepository::class)]
#[ApiResource(
    collectionOperations: [
        "post" => [
            "security" => "is_granted('ROLE_ADMIN')",
            "security_message" => "Seulement les administrateurs peuvent ajouter un tableau.",
        ],
    ],
    itemOperations: [
        "get" => [
            "security" => "is_granted('ROLE_ARTIST') or is_granted('ROLE_MODERATOR')",
            "security_message" => "Tous le monde peut voir un tableau apart les visiteurs.",
        ],
        "put" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN') or (object.owner == user and previous_object.owner == user)",
            "security_post_denormalize_message" => "Seulement l'artiste courant et/ou les administrateurs peuvent modifier un tableau.",
        ],
        "delete" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN')",
            "security_post_denormalize_message" => "Seulement les administrateurs peuvent supprimer un tableau.",
        ],
    ],
    attributes: ["security" => "is_granted('ROLE_ARTIST')"],
)]
class Board
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\ManyToOne(targetEntity: Gallery::class, inversedBy: 'boards')]
    private $gallery;

    #[ORM\Column(type: 'string', enumType: OrientationEnum::class)]
    private $orientation;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getGallery(): ?Gallery
    {
        return $this->gallery;
    }

    public function setGallery(?Gallery $gallery): self
    {
        $this->gallery = $gallery;

        return $this;
    }

    public function getOrientation(): ?string
    {
        return $this->orientation;
    }

    public function setOrientation(string $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }
}
