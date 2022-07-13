<?php


namespace App\Entity;


use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReactionRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => [
                'groups' => ['read:Gallery:collection','read:Gallery:item','read:Board'],
                'enable_max_depth' => true
            ],
        ],
    ],
    itemOperations: [
        'post' => [
            'normalization_context' => [
                'groups' => ['read:Gallery:collection','read:Gallery:item','read:Board'],
                'enable_max_depth' => true
            ],
        ],
    ],
    denormalizationContext: ['groups' => ['write:Reaction']],
    normalizationContext: ['groups' => ['read:Reaction:collection']],
)]
class Reaction
{
    #[ORM\Id]
    #[ORM\Column(type: "uuid", unique: true)]
    #[ORM\GeneratedValue(strategy: "CUSTOM")]
    #[ORM\CustomIdGenerator(class: "doctrine.uuid_generator")]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['read:Reaction:collection', 'write:Reaction'])]
    private $reaction;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\ManyToOne(targetEntity: Work::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['write:Reaction'])]
    private $work;

    #[ORM\Column(type: "uuid", unique: true)]
    #[Groups(['write:Reaction'])]
    private $visitor;

    public function __construct() {
        $this->setCreatedAt(new \DateTime('now'));
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getReaction()
    {
        return $this->reaction;
    }

    /**
     * @param mixed $reaction
     */
    public function setReaction($reaction): void
    {
        $this->reaction = $reaction;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return mixed
     */
    public function getWork()
    {
        return $this->work;
    }

    /**
     * @param mixed $work
     */
    public function setWork($work): void
    {
        $this->work = $work;
    }

    /**
     * @return mixed
     */
    public function getVisitor()
    {
        return $this->visitor;
    }

    /**
     * @param mixed $visitor
     */
    public function setVisitor($visitor): void
    {
        $this->visitor = $visitor;
    }
}