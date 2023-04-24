<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Entity\Definition\UUIDEntityTrait;
use App\ApiResource\Enum\ChangesTypeEnum;
use App\Repository\ChangeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\ApiResource\Enum\ChangesProductsEnum;
use App\ApiResource\Enum\ChangesProblemsEnum;
use App\ApiResource\Enum\ChangesContenuEnum;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Entity\Definition\TimeStampableTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use App\State\SetOwnerProcessor;

#[ORM\Entity(repositoryClass: ChangeRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            openapiContext: [
                'summary' => 'Get all changes',
                'description' => 'Get all changes',
            ],
        ),
        new Post(
            securityMessage: 'Only admins can access this resource',
            openapiContext: [
                'summary' => 'Create a new change',
                'description' => 'Create a new change',
                'normalization_context' => ['groups' => ['Change:create']],
            ],
            processor: SetOwnerProcessor::class
        ),
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            securityMessage: 'Only admins can access this resource',
            openapiContext: [
                'summary' => 'Get all changes',
                'description' => 'Get all changes',
            ],
        ),
        new Put(
            security: "object.getOwner() == user",
            securityMessage: 'Only admins can access this resource',
            openapiContext: [
                'summary' => 'Update a change',
                'description' => 'Update a change',
            ],
        ),
        new Delete(
            security: "object.getOwner() == user",
            openapiContext: [
                'summary' => 'Delete a change',
                'description' => 'Delete a change',
            ],
        ),
    ],
    normalizationContext: ['groups' => ['Change:read']],
    denormalizationContext: ['groups' => ['Change:create', 'write:item']],
)]
#[ORM\HasLifecycleCallbacks]
class Change
{
    use UUIDEntityTrait;

    use TimeStampableTrait;

    public function __construct()
    {
        $this->generateUUId();
    }

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[Groups(["Change:read", "read:item"])]
    private $id;

    #[ORM\Column(length: 255, name: "type_change")]
    #[Assert\Choice(callback: [ChangesTypeEnum::class, 'values'], multiple: false)]
    #[Assert\NotBlank]
    #[Groups(["Change:create", "Change:read", "read:item"])]
    private string $type;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Groups(["Change:create", "Change:read", "read:item"])]
    private ?\DateTimeInterface $heure = null;

    #[ORM\Column(type: 'json')]
    #[Assert\Choice(callback: [ChangesContenuEnum::class, 'values'], multiple: true)]
    #[Groups(["Change:create", "Change:read", "read:item"])]
    private ?array $contenu = [];

    #[ORM\Column(nullable: true, type: 'json')]
    #[Assert\Choice(callback: [ChangesProblemsEnum::class, 'values'], multiple: true)]
    #[Groups(["Change:create", "Change:read", "read:item"])]
    private array $problems = [];

    #[Assert\NotBlank]
    #[ORM\Column(type: 'json')]
    #[Assert\Choice(callback: [ChangesProductsEnum::class, 'values'], multiple: true)]
    #[Groups(["Change:create", "Change:read", "read:item"])]
    private array $products;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["Change:create", "Change:read"])]
    private ?string $commentaire = null;

    #[Assert\NotBlank(message: "The child is required")]
    #[ORM\ManyToOne(inversedBy: 'changes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["Change:create", "Change:read", "read:item"])]
    private ?Children $children = null;

    #[ORM\ManyToOne(inversedBy: 'changes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["read:item"])]
    private ?User $owner = null;

    public function getId()
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getHeure(): ?\DateTimeInterface
    {
        return $this->heure;
    }

    public function setHeure(\DateTimeInterface $heure): self
    {
        $this->heure = $heure;

        return $this;
    }

    public function getContenu(): ?array
    {
        return $this->contenu;
    }

    public function setContenu(?array $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getProblems(): ?array
    {
        return $this->problems;
    }

    public function setProblems(?array $problems): self
    {
        $this->problems = $problems;

        return $this;
    }

    public function getProducts(): ?array
    {
        return $this->products;
    }

    public function setProducts(?array $produits): self
    {
        $this->products = $produits;

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
}
