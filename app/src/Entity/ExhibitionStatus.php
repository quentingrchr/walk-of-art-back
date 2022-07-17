<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Config\OrientationEnum;
use App\Config\StatusEnum;
use App\Repository\ExhibitionStatusRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExhibitionStatusRepository::class)]
#[ApiResource(
    collectionOperations: [],
    itemOperations: [
        'get',
    ],
    normalizationContext: ['groups' => ['read:Exhibition:item']],
)]
class ExhibitionStatus implements UserOwnedInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private $id;

    #[ORM\Column(type: 'string', enumType: StatusEnum::class)]
    #[Groups(['read:Exhibition:item','write:ExhibitionStatus:modo'])]
    private $status;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['write:ExhibitionStatus:modo'])]
    private $reason;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['write:ExhibitionStatus:modo'])]
    private $description;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Exhibition::class, inversedBy: 'statutes')]
    #[ORM\JoinColumn(nullable: false)]
    private $exhibition;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getStatus(): ?StatusEnum
    {
        return $this->status;
    }

    public function setStatus(StatusEnum $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(?string $reason): self
    {
        $this->reason = $reason;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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

    public function getExhibition(): ?Exhibition
    {
        return $this->exhibition;
    }

    public function setExhibition(?Exhibition $exhibition): self
    {
        $this->exhibition = $exhibition;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
