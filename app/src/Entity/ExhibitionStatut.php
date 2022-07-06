<?php

namespace App\Entity;

use App\Repository\ExhibitionStatutRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExhibitionStatutRepository::class)]
class ExhibitionStatut
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $status;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $decription;

    #[ORM\Column(type: 'datetime')]
    private $created_at;

    #[ORM\ManyToOne(targetEntity: Exhibition::class, inversedBy: 'exhibitionStatuts')]
    #[ORM\JoinColumn(nullable: false)]
    private $exhibition;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'exhibitionStatuts')]
    private $updatedUser;

    public function __construct()
    {
        $this->updatedUser = new ArrayCollection();
        $this->setCreatedAt(new \DateTime('now'));
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDecription(): ?string
    {
        return $this->decription;
    }

    public function setDecription(?string $decription): self
    {
        $this->decription = $decription;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getExhibition(): ?Exhibition
    {
        return $this->exhibition;
    }

    public function setExhibition(?Exhibition $exhibition): self
    {
        $this->exhibition = $exhibition;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUpdatedUser(): Collection
    {
        return $this->updatedUser;
    }

    public function addUpdatedUser(User $updatedUser): self
    {
        if (!$this->updatedUser->contains($updatedUser)) {
            $this->updatedUser[] = $updatedUser;
        }

        return $this;
    }

    public function removeUpdatedUser(User $updatedUser): self
    {
        $this->updatedUser->removeElement($updatedUser);

        return $this;
    }
}
