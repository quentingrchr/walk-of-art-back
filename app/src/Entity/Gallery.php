<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GalleryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GalleryRepository::class)]
#[ApiResource]
class Gallery
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 6)]
    private $gps_lat;

    #[ORM\Column(type: 'decimal', precision: 9, scale: 6)]
    private $gps_long;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private $Price;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $max_days;

    #[ORM\Column(type: 'datetime_immutable')]
    private $created_at;

    #[ORM\OneToMany(mappedBy: 'gallery', targetEntity: Board::class)]
    private $boards;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'galleries')]
    #[ORM\JoinColumn(nullable: false)]
    private $created_user;

    public function __construct()
    {
        $this->boards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGpsLat(): ?string
    {
        return $this->gps_lat;
    }

    public function setGpsLat(string $gps_lat): self
    {
        $this->gps_lat = $gps_lat;

        return $this;
    }

    public function getGpsLong(): ?string
    {
        return $this->gps_long;
    }

    public function GpsLong(string $gps_long): self
    {
        $this->gps_long = $gps_long;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->Price;
    }

    public function setPrice(string $Price): self
    {
        $this->Price = $Price;

        return $this;
    }

    public function getMaxDays(): ?int
    {
        return $this->max_days;
    }

    public function setMaxDays(?int $max_days): self
    {
        $this->max_days = $max_days;

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

    /**
     * @return Collection<int, Board>
     */
    public function getBoards(): Collection
    {
        return $this->boards;
    }

    public function addBoard(Board $board): self
    {
        if (!$this->boards->contains($board)) {
            $this->boards[] = $board;
            $board->setGallery($this);
        }

        return $this;
    }

    public function removeBoard(Board $board): self
    {
        if ($this->boards->removeElement($board)) {
            // set the owning side to null (unless already changed)
            if ($board->getGallery() === $this) {
                $board->setGallery(null);
            }
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getGallery() === $this) {
                $reservation->setGallery(null);
            }
        }

        return $this;
    }

    public function getCreatedUser(): ?User
    {
        return $this->created_user;
    }

    public function setCreatedUser(?User $created_user): self
    {
        $this->created_user = $created_user;

        return $this;
    }
}
