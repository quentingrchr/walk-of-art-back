<?php

namespace App\Entity;

use App\Config\OrientationEnum;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BoardRepository;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BoardRepository::class)]
#[ApiResource]
class Board
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private $id;

    #[ORM\Column(type: 'datetime_immutable')]
    private $created_at;

    #[ORM\ManyToOne(targetEntity: Gallery::class, inversedBy: 'boards')]
    private $gallery;

    #[ORM\Column(type: 'string', enumType: OrientationEnum::class)]
    private $orientation;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
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
