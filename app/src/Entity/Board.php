<?php

namespace App\Entity;

use App\Config\OrientationEnum;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BoardRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoardRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'post'
    ],
    itemOperations: [
        'get'
    ],
    attributes: ["security" => "is_granted('ROLE_ARTIST') or is_granted('ROLE_MODERATOR')"],
    denormalizationContext: ['groups' => ['write:Board']],
    normalizationContext: ['groups' => ['read:Board','read:Gallery:collection']],
)]
class Board
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    #[Groups(['read:Board'])]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Gallery::class, inversedBy: 'boards')]
    #[Groups(['read:Board','write:Board'])]
    private $gallery;

    #[ORM\Column(type: 'string', enumType: OrientationEnum::class)]
    #[Groups(['read:Board','write:Board'])]
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
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

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

    public function getOrientation(): ?OrientationEnum
    {
        return $this->orientation;
    }

    public function setOrientation(OrientationEnum $orientation): self
    {
        $this->orientation = $orientation;

        return $this;
    }
}
