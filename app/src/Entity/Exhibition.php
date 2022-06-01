<?php

namespace App\Entity;

use App\Repository\ExhibitionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExhibitionRepository::class)]
class Exhibition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'boolean')]
    private $reaction;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private $revision;

    #[ORM\OneToOne(targetEntity: Work::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $work;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'exhibitions')]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private $created_at;

    #[ORM\OneToMany(mappedBy: 'exhibition', targetEntity: ExhibitionStatut::class, orphanRemoval: true)]
    private $exhibitionStatuts;

    public function __construct()
    {
        $this->exhibitionStatuts = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

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

    public function getRevision(): ?self
    {
        return $this->revision;
    }

    public function setRevision(?self $revision): self
    {
        $this->revision = $revision;

        return $this;
    }

    public function getWork(): ?Work
    {
        return $this->work;
    }

    public function setWork(Work $work): self
    {
        $this->work = $work;

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
    public function getExhibitionStatuts(): Collection
    {
        return $this->exhibitionStatuts;
    }

    public function addExhibitionStatut(ExhibitionStatut $exhibitionStatut): self
    {
        if (!$this->exhibitionStatuts->contains($exhibitionStatut)) {
            $this->exhibitionStatuts[] = $exhibitionStatut;
            $exhibitionStatut->setExhibition($this);
        }

        return $this;
    }

    public function removeExhibitionStatut(ExhibitionStatut $exhibitionStatut): self
    {
        if ($this->exhibitionStatuts->removeElement($exhibitionStatut)) {
            // set the owning side to null (unless already changed)
            if ($exhibitionStatut->getExhibition() === $this) {
                $exhibitionStatut->setExhibition(null);
            }
        }

        return $this;
    }
}
