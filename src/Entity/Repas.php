<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\ApiResource\Enum\RepasTimeEnum as EnumRepasTimeEnum;
use App\Entity\Definition\TimeStampableTrait;
use App\Entity\Definition\UUIDEntityTrait;
use App\Repository\RepasRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Uid\Uuid;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Serializer\Filter\GroupFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Link;
use App\State\RepasProcessor;
use App\State\SetOwnerProcessor;
use DateTimeInterface;
use App\State\GetOwnerProvider;

#[ORM\Entity(repositoryClass: RepasRepository::class)]
#[ApiResource(
    operations:[
        new Get(
            security: "is_granted('VIEW', object)",
            securityMessage: "You can only access your own user"
        ),
        new GetCollection(
            security: " is_granted('ROLE_ADMIN')",
            securityMessage: "You can only access your repas for your children",
            provider: GetOwnerProvider::class,
            uriTemplate: '/childrens/{childrenId}/repas',
            uriVariables: [
                'childrenId' => 
                new Link(fromClass: Children::class, fromProperty: 'id', toClass: Repas::class, toProperty: 'children')],
            openapiContext: [
                'summary' => 'Get all repas of a children',
                'description' => 'Get all repas of a user',
            ],
        ),
        new GetCollection(
            security: " is_granted('ROLE_ADMIN')",
            securityMessage: "You can only access your repas for your children",
            provider: GetOwnerProvider::class,
            uriTemplate: '/users/{userId}/repas',
            uriVariables: [
                'userId' => 
                new Link(fromClass: User::class, fromProperty: 'id', toClass: Repas::class, toProperty: 'owner')],
            openapiContext: [
                'summary' => 'Get all repas of a users',
                'description' => 'Get all repas of a user',
            ],
        ),
        new GetCollection(
            security: "is_granted('ROLE_SUPER_ADMIN')",
            openapiContext: [
                'summary' => 'Get all childrens',
                'description' => 'Get all childrens',
            ],
        ),

        new Post(
            processor: RepasProcessor::class,
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_NOUNOU')",
            securityMessage: "You can only access your repas for your children",
        ),
        new Put(
            security: "is_granted('EDIT', object)",
            securityMessage: "You can only access your own user",
            processor: SetOwnerProcessor::class,
        ),
        new Delete(
            security: "is_granted('DELETE', object)",
            securityMessage: "You can only delete your own user",
        )
        ],
    normalizationContext: ['groups' => ['Repas:read']],
    denormalizationContext: ['groups' => ['Repas:create', 'Repas:update']]
)]
#[ORM\HasLifecycleCallbacks]
#[ApiFilter(GroupFilter::class, arguments: ['parameterName' => 'groups','overrideDefaultGroups' => false])]
class Repas
{
    use UUIDEntityTrait;

    use TimeStampableTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[Groups(['Repas:read'])]
    private $id = null;

    #[Groups(['Repas:read', 'Repas:create', 'Repas:update'])]
    #[ORM\Column(length: 255)]
    private ?string $alimentName = null;

    #[Groups(['Repas:read', 'Repas:create', 'Repas:update'])]
    #[ORM\Column(length: 255)]
    private $repasTime;

    #[Groups(['Repas:read', 'Repas:create', 'Repas:update'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $quantity = null;

    #[Groups(['Repas:read', 'Repas:create', 'Repas:update'])]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $commentaire = null;

    #[Groups(['Repas:read', 'Repas:create', 'Repas:update'])]
    #[ORM\ManyToOne(inversedBy: 'repas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Children $children = null;

    #[ORM\ManyToOne(inversedBy: 'repas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    #[Groups(['Repas:read', 'Repas:create', 'Repas:update'])]
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $repasAt = null;

    public function __construct()
    {
        $this->generateUUId();
    }

    public function getAlimentName(): ?string
    {
        return $this->alimentName;
    }

    public function setAlimentName(string $alimentName): self
    {
        $this->alimentName = $alimentName;

        return $this;
    }

    public function getRepasTime(): ?string
    {
        return $this->repasTime;
    }

    public function setRepasTime(string $repasTime): self
    {
        $this->repasTime = $repasTime;

        return $this;
    }

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(?string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): self
    {
        $this->commentaire = $commentaire;

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

    public function getRepasAt(): ?DateTimeInterface
    {
        return $this->repasAt;
    }

    public function setRepasAt(DateTimeInterface $repasAt): self
    {
        $this->repasAt = $repasAt;

        return $this;
    }
}
