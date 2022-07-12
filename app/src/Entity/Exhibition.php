<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Config\OrientationEnum;
use App\Controller\PostExhibitionAction;
use App\Repository\ExhibitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExhibitionRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => [
                'read:Exhibition:collection','read:Work:collection',
                'read:User'
            ]],
        ],
        'post' => [
            'controller' => PostExhibitionAction::class,
        ],
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => [
                'read:Exhibition:collection','read:Exhibition:item','read:Work:collection',
                'read:Board','read:Gallery:collection','read:User'
            ]]
        ],
        'put',
        'delete'
    ],
    denormalizationContext: ['groups' => ['write:Exhibition']],
    normalizationContext: ['groups' => [
        'read:Exhibition:collection',
        'read:Exhibition:item'
    ]],
)]
class Exhibition implements UserOwnedInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    #[Groups(['read:Exhibition:collection','read:Exhibition:Work'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:Exhibition:collection','write:Exhibition'])]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Exhibition:item','write:Exhibition'])]
    private $description;

    #[ORM\Column(type: 'date')]
    #[Groups(['read:Exhibition:collection','write:Exhibition'])]
    private $dateStart;

    #[ORM\Column(type: 'date')]
    #[Groups(['read:Exhibition:collection','write:Exhibition'])]
    private $dateEnd;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:Exhibition:item'])] //,'write:Exhibition'])] // INFO:: True obligatoire
    private $reaction;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:Exhibition:item','write:Exhibition'])]
    private $comment;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private $revision;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:Exhibition:collection'])]
    private $createdAt;

    #[ORM\OneToMany(mappedBy: 'exhibition', targetEntity: ExhibitionStatus::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['read:Exhibition:item'])]
    private $statutes;

    #[ORM\ManyToOne(targetEntity: Work::class, inversedBy: 'exhibitions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Exhibition:collection','write:Exhibition'])]
    private $work;

    #[ORM\ManyToOne(targetEntity: Board::class)]
    #[Groups(['read:Exhibition:collection'])]
    private $board;

    #[ORM\Column(type: 'json', nullable: true)]
    #[ApiProperty(attributes: [
        "openapi_context" => [
            "type" => "array",
            "items" => [
                'type' => 'object',
                'properties' => [
                    'name' => [
                        'type' => 'string',
                    ],
                    'url' => [
                        'type' => 'string',
                    ]
                ]
            ],
            "example" => '[
                {
                    "name": "facebook",
                    "url": "https://facebook.com/"
                },
                {
                    "name": "tipeee",
                    "url": "https://fr.tipeee.com/"
                }
            ]'
        ]
    ])]
    #[Groups(['read:Exhibition:item','write:Exhibition'])]
    private $snapshot;

    #[ApiProperty(writable: true,
        attributes: [
            "openapi_context" => [
                "type" => OrientationEnum::class,
                "example" => "portrait"
            ]
        ])]
    #[Groups(['write:Exhibition'])]
    private $orientation;

    #[ApiProperty(writable: true,
        attributes: [
        "openapi_context" => [
            "type" => "string",
            "example" => "gallery uuid"
        ]
    ])]
    #[Groups(['write:Exhibition'])]
    private $gallery;

    public function __construct()
    {
        $this->statutes = new ArrayCollection();
        $this->setReaction(true);
        $this->setCreatedAt(new \DateTime('now'));
//        $this->addExhibitionStatut((new ExhibitionStatut())->setStatus(StatusEnum::PENDING)->setDescription('Creation of the exhibition')->setExhibition($this));
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getReaction(): ?bool
    {
        return $this->reaction;
    }

    public function setReaction(bool $reaction): self
    {
        $this->reaction = $reaction;

        return $this;
    }

    public function getComment(): ?bool
    {
        return $this->comment;
    }

    public function setComment(bool $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getRevision(): ?self
    {
        return $this->revision;
    }

    public function setRevision(?self $revision): self
    {
        $this->revision = $revision;

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

    /**
     * @return Collection<int, ExhibitionStatus>
     */
    public function getStatutes(): Collection
    {
        return $this->statutes;
    }

    public function addExhibitionStatut(ExhibitionStatus $exhibitionStatut): self
    {
        if (!$this->statutes->contains($exhibitionStatut)) {
            $this->statutes[] = $exhibitionStatut;
            $exhibitionStatut->setExhibition($this);
        }

        return $this;
    }

    public function getWork(): ?Work
    {
        return $this->work;
    }

    public function setWork(?Work $work): self
    {
        $this->work = $work;

        return $this;
    }

    public function getSnapshot(): ?array
    {
        return $this->snapshot;
    }

    public function setSnapshot(?array $snapshot): self
    {
        $this->snapshot = $snapshot;

        return $this;
    }

    public function getBoard(): ?Board
    {
        return $this->board;
    }

    public function setBoard(?Board $board): self
    {
        $this->board = $board;

        return $this;
    }
}
