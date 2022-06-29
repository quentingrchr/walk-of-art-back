<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\ReservationController;
use App\Repository\ReservationRepository;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    collectionOperations: [
        "post" => [
            "security" => "is_granted('ROLE_ARTIST')",
            "security_message" => "Seulement les artistes peuvent créer une réservation.",
        ],
    ],
    itemOperations: [
        'get' => [
            'method' => 'GET',
            'path' => '/reservation/{id}',
            'name' => 'app_api_reservation',
            'controller' => ReservationController::class,
            'read' => false,
            'openapi_context' => [
                'summary' => "Récupérer la réservation & l'exhibition de l'utilisateur"
            ]
        ],
        "put" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN') or (object.owner == user and previous_object.owner == user)",
            "security_post_denormalize_message" => "Seulement l'artiste courant et/ou les administrateurs peuvent modifier une réservation.",
        ],
        "delete" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN')",
            "security_post_denormalize_message" => "Seulement l'artiste courant et/ou les administrateurs peuvent supprimer une réservation.",
        ],
    ],
    attributes: ["security" => "is_granted('ROLE_ARTIST')"],
)]
class Reservation
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private $id;

    #[ORM\Column(type: 'date')]
    private $date_start;

    #[ORM\Column(type: 'integer')]
    private $duration;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\ManyToOne(targetEntity: Exhibition::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $exhibition;

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
        return $this->date_start;
    }

    public function setDateStart(\DateTimeInterface $date_start): self
    {
        $this->date_start = $date_start;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
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

    public function getExhibition(): ?Exhibition
    {
        return $this->exhibition;
    }

    public function setExhibition(?Exhibition $exhibition): self
    {
        $this->exhibition = $exhibition;

        return $this;
    }
}
