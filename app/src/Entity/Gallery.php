<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetAvailableGalleriesAction;
use App\Repository\GalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use App\Config\OrientationEnum;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'denormalization_context' => ['groups' => ['write:Gallery']],
            'normalization_context' => ['groups' => ['read:Gallery:collection','read:Gallery:item','read:Board']],
        ],
        'get_available_galleries' => [
            'method' => 'POST',
            'path' => '/galleries/available',
            'deserialize' => false,
            'controller' => GetAvailableGalleriesAction::class,
            'openapi_context' => [
                'summary' => "Get all galleries that have board dates and orientation available",
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema'  => [
                                'type'       => 'object',
                                'properties' =>
                                    [
                                        'dateStart'    => ['type' => 'date'],
                                        'dateEnd'      => ['type' => 'date'],
                                        'orientation'   => OrientationEnum::class,
                                    ],
                            ],
                            'example' => [
                                "dateStart"    => "2022-07-09",
                                "dateEnd"      => "2022-07-11",
                                "orientation"   => "portrait"
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

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
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
