<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\SleepRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTimeInterface;
use App\Entity\Definition\TimeStampableTrait;
use App\Entity\Definition\UUIDEntityTrait;
use ApiPlatform\Metadata\Link;
use App\State\GetOwnerProvider;
use App\State\CheckOwnProcessor;

#[ORM\Entity(repositoryClass: SleepRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            security: " is_granted('ROLE_ADMIN')",
            securityMessage: "You can only access your sleep for your children",
            provider: GetOwnerProvider::class,
            uriTemplate: '/childrens/{childrenId}/sleeps',
            uriVariables: [
                'childrenId' => 
                new Link(fromClass: Children::class, fromProperty: 'id', toClass: Sleep::class, toProperty: 'children')],
            openapiContext: [
                'summary' => 'Get all sleep of a children',
                'description' => 'Get all sleep of a user',
            ],
        ),
        new GetCollection(
            security: " is_granted('ROLE_ADMIN')",
            securityMessage: "You can only access your sleep for your children",
            provider: GetOwnerProvider::class,
            uriTemplate: '/users/{userId}/sleeps',
            uriVariables: [
                'userId' => 
                new Link(fromClass: User::class, fromProperty: 'id', toClass: Sleep::class, toProperty: 'owner')],
            openapiContext: [
                'summary' => 'Get all sleep of a users',
                'description' => 'Get all sleep of a user',
            ],
        ),
        new Get(
            openapiContext: [
                'summary' => 'Get a sleep',
                'description' => 'Get a sleep',
            ],
            security: "is_granted('ROLE_ADMIN') and is_granted('VIEW', object)",
            securityMessage: "You can only access your own user"
        ),
        new Post(
            processor: CheckOwnProcessor::class,
            securityPostDenormalize: "is_granted('SLEEP_ADD', object)",
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: "You can only access your sleep for your children",
        ),
        new Put(
            security: "is_granted('EDIT', object)",
            securityMessage: "You can only access your own user",
            processor: CheckOwnProcessor::class,
        ),
        new Delete(
            security: "is_granted('DELETE', object)",
            securityMessage: "You can only delete your own user",
        ),
    ],
    normalizationContext: ['groups' => ['Sleep:read']],
    denormalizationContext: ['groups' => ['Sleep:create', 'write:item']],
)]
#[ORM\HasLifecycleCallbacks]
class Sleep
{
    use UUIDEntityTrait;

    use TimeStampableTrait;

    public function __construct()
    {
        $this->generateUUId();
    }

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[Groups(["Sleep:read", "read:item"])]
    private $id;

    #[Groups(["Sleep:read", "read:item", "Sleep:create"])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startedAt = null;

    #[Groups(["Sleep:read", "read:item", "Sleep:create"])]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endAt = null;

    #[Groups(["Sleep:read", "read:item", "Sleep:create"])]
    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $timeSleep = null;

    #[Groups(["Sleep:read", "read:item", "Sleep:create"])]
    #[ORM\ManyToOne(inversedBy: 'sleeps')]
    private ?Children $children = null;

    #[ORM\ManyToOne]
    private ?User $owner = null;

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getTimeSleep(): ?string
    {
        return $this->timeSleep;
    }

    public function setTimeSleep(?string $timeSleep): self
    {
        $this->timeSleep = $timeSleep;

        return $this;
    }

    public function getChildren(): ?Children
    {
        return $this->children;
    }

    public function setChildren(?Children $children): self
    {
        $this->children = $children;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}
