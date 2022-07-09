<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\WorkFilesRepository;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @Vich\Uploadable
 */
#[ORM\Entity(repositoryClass: WorkFilesRepository::class)]
#[ApiResource(
    collectionOperations: [],
    itemOperations: ['get']
)]
class WorkFiles
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    #[Groups(['read:Work:collection'])]
    private $id;

    /**
     * @Vich\UploadableField(mapping="post_work_files", fileNameProperty="pathFile")
     */
    private ?File $file;

    #[ORM\Column(type: 'string', length: 255)]
    private $pathFile;

    #[Groups(['read:Work:collection'])]
    private ?string $fileUrl;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Work::class, inversedBy: 'workFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private $work;

    public function __construct()
    {
        $this->setCreatedAt(new \DateTime('now'));
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getPathFile(): ?string
    {
        return $this->pathFile;
    }

    public function setPathFile(string $pathFile): self
    {
        $this->pathFile = $pathFile;

        return $this;
    }

    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }

    public function setFileUrl(string $fileUrl): self
    {
        $this->fileUrl = $fileUrl;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;
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

    public function getWork(): ?Work
    {
        return $this->work;
    }

    public function setWork(?Work $work): self
    {
        $this->work = $work;

        return $this;
    }
}
