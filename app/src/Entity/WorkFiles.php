<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetWorkController;
use App\Controller\GetWorkFileController;
use App\Controller\WorkController;
use App\Controller\PostWorkFilesController;
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
    collectionOperations: [
        'post' => [
            "security" => "is_granted('ROLE_ARTIST')",
            "security_message" => "Only artists.",
        ],
    ],
    itemOperations: [
        'get' => [
            'method' => 'GET',
            'path' => '/work-file/{id}',
            'controller' => GetWorkFileController::class,
            'read' => true,
            'openapi_context' => [
                'summary' => "Get a work file of a current user"
            ],
            'normalization_context' => ['groups' => ['read:WorkFile:item']],
        ],
        "put" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN') or (object.owner == user and previous_object.owner == user)",
            "security_post_denormalize_message" => "Only artists and admins.",
        ],
        "delete" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN') or (object.owner == user and previous_object.owner == user)",
            "security_post_denormalize_message" => "Only artists and admins.",
        ],
    ],
    attributes: ["security" => "is_granted('ROLE_ARTIST')"],
)]
class WorkFiles
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    #[Groups(['read:Work:collection','read:WorkFile:item', 'read:Work:child'])]
    private $id;

    /**
     * @Vich\UploadableField(mapping="post_work_files", fileNameProperty="pathFile")
     */
    private ?File $file;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:WorkFile:item','read:WorkFile:collection', 'read:Work:child'])]
    private $pathFile;

    #[Groups(['read:Work:collection','read:WorkFile:item'])]
    private ?string $fileUrl;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read:WorkFile:item','read:WorkFile:collection', 'read:Work:child'])]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Work::class, cascade: ['persist', 'remove'], inversedBy: 'work_files')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read:WorkFile:item','read:WorkFile:collection'])]
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

    public function jsonSerialize()
    {
        return array(
            'id' => $this->getId(),
            'pathFile'=> $this->getPathFile(),
            'main'=> $this->getMain(),
            'workId'=> $this->getWork()->getId(),
        );
    }
}
