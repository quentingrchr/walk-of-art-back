<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\UserController;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    collectionOperations: [],
    itemOperations: [
    'get' => [
            'method' => 'GET',
            'name' => 'app_api_artists',
            'controller' => UserController::class,
            'openapi_context' => [
                'summary' => "Récupérer tout les artistes | Only Moderators & Admins"
            ]
        ],
        'update_profile' => [
            'method' => 'POST',
            'path' => '/update-profile',
            'controller' => UserController::class,
            'read' => false,
        ],
    ],
    attributes: ["security" => "is_granted('ROLE_ARTIST')"],
    normalizationContext: ['groups' => ['read:User']],
)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, JWTUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    #[Groups(['read:User'])]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['read:User'])]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:User'])]
    private $firstname;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read:User'])]
    private $lastname;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Work::class)]
    private $works;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Exhibition::class)]
    private $exhibitions;

    #[ORM\ManyToMany(targetEntity: ExhibitionStatut::class, mappedBy: 'updatedUser')]
    private $exhibitionStatuts;

    #[ORM\OneToMany(mappedBy: 'created_user', targetEntity: Gallery::class)]
    private $galleries;

    public function __construct()
    {
        $this->works = new ArrayCollection();
        $this->exhibitions = new ArrayCollection();
        $this->exhibitionStatuts = new ArrayCollection();
        $this->galleries = new ArrayCollection();
        $this->setCreatedAt(new \DateTime('now'));
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

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

    /**
     * @return Collection<int, Work>
     */
    public function getWorks(): Collection
    {
        return $this->works;
    }

    public function addWork(Work $work): self
    {
        if (!$this->works->contains($work)) {
            $this->works[] = $work;
            $work->setUser($this);
        }

        return $this;
    }

    public function removeWork(Work $work): self
    {
        if ($this->works->removeElement($work)) {
            // set the owning side to null (unless already changed)
            if ($work->getUser() === $this) {
                $work->setUser(null);
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
            $exhibition->setUser($this);
        }

        return $this;
    }

    public function removeExhibition(Exhibition $exhibition): self
    {
        if ($this->exhibitions->removeElement($exhibition)) {
            // set the owning side to null (unless already changed)
            if ($exhibition->getUser() === $this) {
                $exhibition->setUser(null);
            }
        }

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
            $exhibitionStatut->addUpdatedUser($this);
        }

        return $this;
    }

    public function removeExhibitionStatut(ExhibitionStatut $exhibitionStatut): self
    {
        if ($this->exhibitionStatuts->removeElement($exhibitionStatut)) {
            $exhibitionStatut->removeUpdatedUser($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Gallery>
     */
    public function getGalleries(): Collection
    {
        return $this->galleries;
    }

    public function addGallery(Gallery $gallery): self
    {
        if (!$this->galleries->contains($gallery)) {
            $this->galleries[] = $gallery;
            $gallery->setUser($this);
        }

        return $this;
    }

    public function removeGallery(Gallery $gallery): self
    {
        if ($this->galleries->removeElement($gallery)) {
            // set the owning side to null (unless already changed)
            if ($gallery->getUser() === $this) {
                $gallery->setUser(null);
            }
        }

        return $this;
    }

    public static function createFromPayload($id, array $payload): self
    {
        $user = new User();
        $user->setId($payload['id']);
        $user->setEmail($payload['email']);
        $user->setRoles($payload['roles']);
        return $user;
    }
}
