<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\WorkFilesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkFilesRepository::class)]
#[ApiResource]
class WorkFiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $path_file;

    #[ORM\Column(type: 'boolean')]
    private $main;

    #[ORM\ManyToOne(targetEntity: Work::class, inversedBy: 'work_files')]
    #[ORM\JoinColumn(nullable: false)]
    private $work;

    public function getId(): ?int
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
