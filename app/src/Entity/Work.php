<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\WorkController;
use App\Controller\PostWorkFilesController;
use App\Repository\WorkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkRepository::class)]
#[ApiResource(
    collectionOperations: [
        "post" => [
            "security" => "is_granted('ROLE_ARTIST')",
            "security_message" => "Only artists.",
        ],
        'get' => [
            'method' => 'GET',
            'path' => '/api/works',
            'controller' => WorkController::class,
            'read' => false,
            'openapi_context' => [
                'summary' => "Get all works of current user"
            ]
        ],
    ],
    itemOperations: [
        'get' => [
            'method' => 'GET',
            'path' => '/api/work/{id}',
            'controller' => WorkController::class,
            'read' => false,
            'openapi_context' => [
                'summary' => "Get a work of a current user"
            ],
            "security" => "is_granted('ROLE_VISITOR')",
            "security_message" => "Tous le monde peut voir les travaux apart les visiteurs.",
            'normalization_context' => [
                'groups' => ['read:Work:collection','read:Work:item','read:User','read:Exhibition:collection'],
                'enable_max_depth' => true
            ],
        ],
        'post_files' => [
            'method' => 'POST',
            'path' => '/works/{id}',
            'deserialize' => false,
            'controller' => PostWorkFilesController::class,
            'normalization_context' => ['groups' => ['read:Work:collection','read:Work:item','read:User']],
            'openapi_context' => [
                'summary' => 'Add file(s)',
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ],
                                    'mainFile' => [
                                        'type' => 'string',
                                        'format' => 'binary',
                                        'description' => 'If null the first \'file\' is used for mainFile'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ],
        "put" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN') or (object.owner == user and previous_object.owner == user)",
            "security_post_denormalize_message" => "Seulement l'artiste courant et/ou les administrateurs peuvent modifier un travail.",
            'denormalization_context' => ['groups' => ['write:Work']],
//            'normalization_context' => ['groups' => ['read:Work:collection','read:Work:item','read:User']],
        ],
        "delete" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN') or (object.owner == user and previous_object.owner == user)",
            "security_post_denormalize_message" => "Seulement l'artiste courant et/ou les administrateurs peuvent supprimer un travail.",
        ],
    ],
    attributes: ["security" => "is_granted('ROLE_ARTIST')"],
    denormalizationContext: ['groups' => ['write:Work']],
    normalizationContext: ['groups' => ['read:Work:collection']],
)]
class Work implements UserOwnedInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups(['read:Work:collection'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:Work:collection', 'write:Work'])]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Work:collection', 'write:Work'])]
    private $description;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:Work:item'])]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'works')]
    #[ORM\JoinColumn(nullable: false)]
//    #[Groups(['read:Work:item'])]
//    #[MaxDepth(1)]
    private $user;

    #[ORM\OneToOne(targetEntity: WorkFiles::class, cascade: ['remove'])]
    #[Groups(['read:Work:collection', 'write:Work'])]
    #[MaxDepth(1)]
    private $mainFile;

    #[ORM\OneToMany(mappedBy: 'work', targetEntity: WorkFiles::class, orphanRemoval: true)]
    #[Groups(['read:Work:item', 'write:Work'])]
    #[MaxDepth(1)]
    private $workFiles;

    #[ORM\OneToMany(mappedBy: 'work', targetEntity: Exhibition::class)]
    #[Groups(['read:Work:item'])]
    #[MaxDepth(1)]
    private $exhibitions;

    public function __construct()
    {
        $this->workFiles = new ArrayCollection();
        $this->setCreatedAt(new \DateTime('now'));
        $this->exhibitions = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getMainFile(): ?WorkFiles
    {
        return $this->mainFile;
    }

    public function setMainFile(?WorkFiles $mainFile): self
    {
        $this->mainFile = $mainFile;

        return $this;
    }

    /**
     * @return Collection<int, WorkFiles>
     */
    public function getWorkFiles(): Collection
    {
        return $this->workFiles;
    }

    public function addWorkFile(WorkFiles $workFile): self
    {
        if (!$this->workFiles->contains($workFile)) {
            $this->workFiles[] = $workFile;
            $workFile->setWork($this);
        }

        return $this;
    }

    public function removeWorkFile(WorkFiles $workFile): self
    {
        if ($this->workFiles->removeElement($workFile)) {
            // set the owning side to null (unless already changed)
            if ($workFile->getWork() === $this) {
                $workFile->setWork(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Exhibition>
     */
    public function getExhibitions(): Collection
    {
        return $this->exhibitions;
    }

    public function addExhibition(Exhibition $exhibition): self
    {
        if (!$this->exhibitions->contains($exhibition)) {
            $this->exhibitions[] = $exhibition;
            $exhibition->setWork($this);
        }

        return $this;
    }

    public function removeExhibition(Exhibition $exhibition): self
    {
        if ($this->exhibitions->removeElement($exhibition)) {
            // set the owning side to null (unless already changed)
            if ($exhibition->getWork() === $this) {
                $exhibition->setWork(null);
            }
        }

        return $this;
    }

    public function arrayOfWorkFile()
    {
        $arrayOfWorkFiles = [];
        foreach($this->getWorkFiles() as $workFile) {
            $arrayOfWorkFiles[] = $workFile->getId();
        }

        return $arrayOfWorkFiles;
    }

    public function jsonSerialize()
    {
        return array(
            'id' => $this->getId(),
            'title'=> $this->getTitle(),
            'description'=> $this->getDescription(),
            'createdAt'=> $this->getCreatedAt(),
            'userId'=> $this->getUser()->getId(),
            'workFilesIds'=> $this->arrayOfWorkFile(),
        );
    }
}
