<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetGalleryAction;
use App\Repository\GalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'denormalization_context' => ['groups' => ['write:Gallery']],
            'normalization_context' => ['groups' => ['read:Gallery:collection','read:Gallery:item','read:Board']],
        ],
        'get_available_galleries' => [
            'method' => 'GET',
            'path' => '/galleries/available',
            'deserialize' => false,
            'controller' => GetGalleryAction::class,
            'openapi_context' => [
                'summary' => "Get all galleries that have board dates and orientation available",
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema'  => [
                                'type'       => 'object',
                                'properties' =>
                                    [
                                        'date_start'    => ['type' => 'string'],
                                        'date_end'      => ['type' => 'string'],
                                        'orientation'   => ['type' => 'string'],
                                    ],
                            ],
                            'example' => [
                                "date_start"    => "2022-07-09",
                                "date_end"      => "2022-07-11",
                                "orientation"   => "vertical"
                            ],
                        ],
                    ]
                ]
            ],
            'normalization_context' => ['groups' => ['read:Gallery:collection', 'read:Gallery:items']]
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:Gallery:collection','read:Gallery:item','read:Board'],
                'enable_max_depth' => true
            ],
        ],
    ],
    normalizationContext: ['groups' => ['read:Gallery:collection']],
)]
class Gallery
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    #[Groups(['read:Gallery:collection'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:Gallery:collection','write:Gallery'])]
    private $name;

    #[ORM\Column(type: 'float', precision: 8, scale: 6)]
    #[Groups(['read:Gallery:collection','write:Gallery'])]
    private $latitude;

    #[ORM\Column(type: 'float', precision: 9, scale: 6)]
    #[Groups(['read:Gallery:collection','write:Gallery'])]
    private $longitude;

    #[ORM\Column(type: 'float', precision: 5, scale: 2)]
    #[Groups(['read:Gallery:item','write:Gallery'])]
    private $price;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Groups(['read:Gallery:item','write:Gallery'])]
    private $maxDays;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\OneToMany(mappedBy: 'gallery', targetEntity: Board::class, cascade: ['persist'])]
    #[Groups(['read:Gallery:item'])]
    #[MaxDepth(1)]
    private $boards;

    public function __construct()
    {
        $this->boards = new ArrayCollection();
        $this->setCreatedAt(new \DateTime('now'));
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getMaxDays(): ?int
    {
        return $this->maxDays;
    }

    public function setMaxDays(?int $maxDays): self
    {
        $this->maxDays = $maxDays;

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

    /**
     * @return Collection<int, Board>
     */
    public function getBoards(): Collection
    {
        return $this->boards;
    }

    public function addBoard(Board $board): self
    {
        if (!$this->boards->contains($board)) {
            $this->boards[] = $board;
            $board->setGallery($this);
        }

        return $this;
    }

    public function removeBoard(Board $board): self
    {
        if ($this->boards->removeElement($board)) {
            // set the owning side to null (unless already changed)
            if ($board->getGallery() === $this) {
                $board->setGallery(null);
            }
        }

        return $this;
    }
}
