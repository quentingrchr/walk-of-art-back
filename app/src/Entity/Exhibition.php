<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Config\StatusEnum;
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
                'read:Reservation:collection',/*'read:Board','read:Gallery:collection',*/
                'read:User'
            ]],
        ],
        'post',
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => [
                'read:Exhibition:collection','read:Exhibition:item','read:Work:collection',
                'read:Reservation:collection','read:User'
            ]]
        ],
        'put',
        'delete'
    ],
    denormalizationContext: ['groups' => ['write:Exhibition']],
    normalizationContext: ['groups' => [
        'read:Exhibition:collection'
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
    #[Groups(['read:Work:collection'])]
    private $createdAt;

    #[ORM\OneToMany(mappedBy: 'exhibition', cascade: ['persist'], targetEntity: ExhibitionStatut::class, orphanRemoval: true)]
    #[Groups(['read:Exhibition:item'/*,'read:Exhibition:Work'*/])] // WARNING :: pas besoin pour les work
    private $statuts;

    #[ORM\ManyToOne(targetEntity: Work::class, inversedBy: 'exhibitions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:Exhibition:collection','write:Exhibition'])]
    private $work;

    #[ORM\OneToMany(mappedBy: 'exhibition', targetEntity: Reservation::class, orphanRemoval: true)]
    #[Groups(['read:Exhibition:collection','read:Exhibition:Work'])]
//    #[MaxDepth(1)]
    private $reservations;

//    #[ORM\Column(type: 'json', nullable: true)]
//    #[ApiProperty(attributes: [
//        "openapi_context" => [
//            "type" => "array",
//            "items" => ["type" => "integer"]
//        ],
//        "json_schema_context" => [ // <- MAGIC IS HERE, you can override the json_schema_context here.
//            "type" => "array",
//            "items" => ["type" => "integer"]
//        ]
//    ])]
//    private $snapshot = [];

    // TODO :: Snapshot(array{name,url})

    public function __construct()
    {
        $this->statuts = new ArrayCollection();
        $this->reservations = new ArrayCollection();
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
     * @return Collection<int, ExhibitionStatut>
     */
    public function getStatuts(): Collection
    {
        return $this->statuts;
    }

    public function addExhibitionStatut(ExhibitionStatut $exhibitionStatut): self
    {
        if (!$this->statuts->contains($exhibitionStatut)) {
            $this->statuts[] = $exhibitionStatut;
            $exhibitionStatut->setExhibition($this);
        }

        return $this;
    }

/*    public function removeExhibitionStatut(ExhibitionStatut $exhibitionStatut): self
    {
        if ($this->statuts->removeElement($exhibitionStatut)) {
            // set the owning side to null (unless already changed)
            if ($exhibitionStatut->getExhibition() === $this) {
                $exhibitionStatut->setExhibition(null);
            }
        }

        return $this;
    }*/

    public function getWork(): ?Work
    {
        return $this->work;
    }

    public function setWork(?Work $work): self
    {
        $this->work = $work;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(ExhibitionStatut $reservations): self
    {
        if (!$this->reservations->contains($reservations)) {
            $this->reservations[] = $reservations;
            $reservations->setExhibition($this);
        }

        return $this;
    }

    public function removeReservation(ExhibitionStatut $reservations): self
    {
        if ($this->statuts->removeElement($reservations)) {
            // set the owning side to null (unless already changed)
            if ($reservations->getExhibition() === $this) {
                $reservations->setExhibition(null);
            }
        }

        return $this;
    }

//    public function getSnapshot(): ?array
//    {
//        return $this->snapshot;
//    }
//
//    public function setSnapshot(?array $snapshot): self
//    {
//        $this->snapshot = $snapshot;
//
//        return $this;
//    }
}
