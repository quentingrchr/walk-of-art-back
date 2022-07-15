<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\GetReactionsAction;
use App\Controller\PostReactionAction;
use App\Repository\ReactionRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ReactionRepository::class)]
#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'GET',
            'path' => '/reactions/{boardId}',
            'deserialize' => false,
            "read" => false,
            'controller' => GetReactionsAction::class,
            'normalization_context' => [
                'groups' => ['read:Reaction:collection'],
            ],
        ],
    ],
    itemOperations: [
        'get',
        'post' => [
            'method' => 'POST',
            'path' => '/reactions/{boardId}',
            'deserialize' => false,
            "read" => false,
            'controller' => PostReactionAction::class,
            'openapi_context' => [
                'summary' => "Post reaction",
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema'  => [
                                'type'       => 'object',
                                'properties' =>
                                    [
                                        'visitorId'   => ['type' => 'string'],
                                        'reaction'  => ['type' => 'string'] /*ReactionEnum::class*/,
                                    ],
                            ],
                            'example' => [
                                "visitorId" => "khubvjlbjb",
                                "reaction"   => "like"
                            ],
                        ],
                    ]
                ]
            ],
            'normalization_context' => [
                'groups' => ['write:Reaction', 'read:Reaction:collection'],
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

    #[ORM\ManyToOne(targetEntity: Exhibition::class, inversedBy: 'reactions')]
    #[ORM\JoinColumn(nullable: false)]
    private $exhibition;

    #[ORM\Column(type: "string", unique: false)]
    #[Groups(['write:Reaction'])]
    private $visitor;

    public function __construct() {
        $this->setCreatedAt(new \DateTime('now'));
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getReaction(): ?string
    {
        return $this->reaction;
    }

    public function setReaction(?string $reaction): self
    {
        $this->reaction = $reaction;

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

    public function getExhibition(): ?Exhibition
    {
        return $this->exhibition;
    }

    public function setExhibition(?Exhibition $exhibition): self
    {
        $this->exhibition = $exhibition;

        return $this;
    }

    public function getVisitor(): ?string
    {
        return $this->visitor;
    }

    public function setVisitor(?string $visitor): self
    {
        $this->visitor = $visitor;

        return $this;
    }
}