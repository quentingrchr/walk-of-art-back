<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetReservationController;
use App\Controller\GetReservationsController;
use App\Controller\ReservationController;
use App\Repository\ReservationRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'GET',
            'path' => '/reservations',
            'controller' => GetReservationsController::class,
            'read' => true,
            'openapi_context' => [
                'summary' => "Get all reservations of current user"
            ],
            'normalization_context' => ['groups' => ['read:Reservation:collection', 'read:Exhibition:collection', 'read:Work:child', 'read:User']],
        ],
        'post' => [
            "security" => "is_granted('ROLE_ARTIST')",
            "security_message" => "Post reservations, role Artist only.",
            'denormalization_context' => ['groups' => ['write:Reservation','write:Exhibition']],
            'normalization_context' => ['groups' => ['read:Reservation:collection','read:Reservation:item','read:User']]
        ],
    ],
    itemOperations: [
        'get' => [
            'method' => 'GET',
            'path' => '/reservation/{id}',
            'controller' => GetReservationController::class,
            'read' => true,
            'openapi_context' => [
                'summary' => "Get an exhibition of current user"
            ],
            'normalization_context' => ['groups' => ['read:Reservation:item', 'read:Exhibition:collection', 'read:Work:child', 'read:User']],
            'enable_max_depth' => true
        ],
        "put" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN') or (object.owner == user and previous_object.owner == user)",
            "security_post_denormalize_message" => "Only artists and admins.",
        ],
        "delete" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN')",
            "security_post_denormalize_message" => "Only artists and admins.",
        ],
    ],
    denormalizationContext: ['groups' => ['write:Reservation']],
    normalizationContext: ['groups' => ['read:Reservation:collection']],
)]
class Reservation
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    #[Groups(['read:Reservation:collection','read:Reservation:item', 'read:Reservation:child', 'read:Exhibition:child'])]
    private $id;

    #[ORM\Column(type: 'date')]
    #[Groups(['read:Reservation:collection','write:Reservation','read:Reservation:item', 'read:Reservation:child', 'read:Exhibition:child'])]
    private $dateStart;

    #[ORM\Column(type: 'date')]
    #[Groups(['read:Reservation:collection','write:Reservation','read:Reservation:item', 'read:Reservation:child', 'read:Exhibition:child'])]
    private $dateEnd;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:Reservation:collection', 'read:Reservation:item', 'read:Reservation:child', 'read:Exhibition:child'])]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Exhibition::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Reservation:collection','write:Reservation','read:Reservation:item'])]
    #[MaxDepth(3)]
    private $exhibition;

    // TODO :: gérer orientation

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

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

    public function jsonSerialize()
    {
        return array(
            'id' => $this->getId(),
            'dateStart'=> $this->getDateStart(),
            'duration'=> $this->getDuration(),
            'createdAt'=> $this->getCreatedAt(),
            'exhibitionId'=> $this->getExhibition()->getId(),
        );
    }
}
