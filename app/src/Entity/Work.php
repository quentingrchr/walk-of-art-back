<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\PostWorkFilesController;
use App\Controller\PutWorkUpdatedAtAction;
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
        'get' => [
            'normalization_context' => [
                'groups' => ['read:Work:collection','read:Work:item',
                    'read:Exhibition:Work',
                    /*'read:Board',*/'read:User'
                ],
                'enable_max_depth' => true
            ]
        ],
        'post',
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:Work:collection','read:Work:item',
                    'read:Exhibition:collection',
                    'read:Board','read:Gallery:collection',
                    'read:User'],
                'enable_max_depth' => true
            ]
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
        'put' => [
            'method' => 'PUT',
            'path' => '/works/{id}',
            'controller' => PutWorkUpdatedAtAction::class,
            'denormalization_context' => ['groups' => ['write:Work']],
//            'normalization_context' => ['groups' => ['read:Work:collection','read:Work:item','read:User']],
        ],
        'delete'
    ],
    attributes: ["security" => "is_granted('ROLE_ARTIST') or is_granted('ROLE_MODERATOR')"],
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
    #[Groups(['read:Work:collection','write:Work'])]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['read:Work:item','write:Work'])]
    private $description;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:Work:item'])]
    private $createdAt;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:Work:collection', 'read:Work:item'])]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\OneToOne(targetEntity: WorkFiles::class, cascade: ['remove'])]
    #[Groups(['read:Work:collection'])]
//    #[MaxDepth(1)]
    private $mainFile;

    #[ORM\OneToMany(mappedBy: 'work', targetEntity: WorkFiles::class, orphanRemoval: true)]
    #[Groups(['read:Work:item'])]
//    #[MaxDeptch(1)]
    private $workFiles;

    #[ORM\OneToMany(mappedBy: 'work', targetEntity: Exhibition::class)]
    #[Groups(['read:Work:item'])]
    #[MaxDepth(1)]
    private $exhibitions;

    public function __construct()
    {
        $this->workFiles = new ArrayCollection();
        $this->setCreatedAt(new \DateTime('now'));
        $this->setUpdatedAt(new \DateTime('now'));
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

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }
    public function setUpdatedAt(\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
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
}
