<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Config\OrientationEnum;
use App\Config\StatusEnum;
use App\Controller\Moderator\GetExhibitionToModerateAction;
use App\Controller\Moderator\PostExhibitionStatusAction;
use App\Controller\PostExhibitionAction;
use App\Repository\ExhibitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ExhibitionRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => [
                'read:Exhibition:collection','read:Work:collection'
            ]],
        ],
        'get_moderation' => [
            'method' => 'GET',
            'path' => '/moderation/exhibitions',
            "security" => "is_granted('ROLE_MODERATOR')",
            'controller' => GetExhibitionToModerateAction::class,
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
        'post_moderation' => [
            'method' => 'post',
            'path' => '/moderation/exhibitions/{id}',
            "security" => "is_granted('ROLE_MODERATOR')",
            'controller' => PostExhibitionStatusAction::class,
            'denormalization_context' => ['groups' => ['write:ExhibitionStatus:modo']],
            'openapi_context' => [
                'summary' => '[Only moderator] Post a moderation',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'status' => [
                                        'type' => 'string',
                                        'enum' => StatusEnum::class
                                    ],
                                    'reason' => [
                                        'type' => 'string',
                                    ],
                                    'description' => [
                                        'type' => 'string',
                                    ],
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'normalization_context' => ['groups' => [
                'read:Exhibition:collection','read:Work:collection',
                'read:User'
            ]],
        ],
        'put',
        'delete'
    ],
    attributes: ["security" => "is_granted('ROLE_ARTIST') or is_granted('ROLE_MODERATOR')"],
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
    private $reaction;

    #[ORM\OneToMany(mappedBy: 'exhibition', targetEntity: Reaction::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinColumn(nullable: true)]
    private $reactions;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['read:Exhibition:item','write:Exhibition'])]
    private $comment;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private $revision;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Exhibition:collection'])]
    private $user;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:Exhibition:collection'])]
    private $createdAt;

    #[ORM\OneToMany(mappedBy: 'exhibition', targetEntity: ExhibitionStatus::class, cascade: ['persist'], orphanRemoval: true)]
    #[Groups(['read:Exhibition:item','write:ExhibitionStatus:modo'])]
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

    public function getReactions()
    {
        return $this->reactions;
    }

    public function removeReactions(Reaction $reaction): self
    {
        if ($this->reaction->removeElement($reaction)) {
            // set the owning side to null (unless already changed)
            if ($reaction->getExhibition() === $this) {
                $reaction->setExhibition(null);
            }
        }

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

    public function addExhibitionStatus(ExhibitionStatus $exhibitionStatus): self
    {
        if (!$this->statutes->contains($exhibitionStatus)) {
            $this->statutes[] = $exhibitionStatus;
            $exhibitionStatus->setExhibition($this);
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
