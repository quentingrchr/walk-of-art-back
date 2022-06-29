<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\WorkFilesRepository;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkFilesRepository::class)]
#[ApiResource(
    collectionOperations: [
        "post" => [
            "security" => "is_granted('ROLE_ARTIST')",
            "security_message" => "Seulement les artistes peuvent ajouter un fichier.",
        ],
    ],
    itemOperations: [
        "get" => [
            "security" => "is_granted('ROLE_VISITOR')",
            "security_message" => "Tous le monde peut voir les fichiers apart les visiteurs.",
        ],
        "put" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN') or (object.owner == user and previous_object.owner == user)",
            "security_post_denormalize_message" => "Seulement l'artiste courant et/ou les administrateurs peuvent modifier un fichier.",
        ],
        "delete" => [
            "security_post_denormalize" => "is_granted('ROLE_ADMIN') or (object.owner == user and previous_object.owner == user)",
            "security_post_denormalize_message" => "Seulement l'artiste courant et/ou les administrateurs peuvent supprimer un fichier.",
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
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $path_file;

    #[ORM\Column(type: 'boolean')]
    private $main;

    #[ORM\ManyToOne(targetEntity: Work::class, inversedBy: 'work_files')]
    #[ORM\JoinColumn(nullable: false)]
    private $work;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getPathFile(): ?string
    {
        return $this->path_file;
    }

    public function setPathFile(string $path_file): self
    {
        $this->path_file = $path_file;

        return $this;
    }

    public function getMain(): ?bool
    {
        return $this->main;
    }

    public function setMain(bool $main): self
    {
        $this->main = $main;

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
