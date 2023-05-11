<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Serializer\Filter\GroupFilter;
use App\Entity\Definition\TimeStampableTrait;
use App\State\UserPasswordHasher;
use App\Entity\Definition\UUIDEntityInterface;
use App\Entity\Definition\UUIDEntityTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\OpenApi\Model\Link;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new GetCollection(
            security: "is_granted('ROLE_ADMIN')",
            openapiContext: [
                'normalization_context' => ['groups' => ['User:read', 'read:collection']],
            ]
        ),
        new Post(
            processor: UserPasswordHasher::class,
            openapiContext: [
                'summary' => 'Create a new user',
                'description' => 'Create a new user',
                'normalization_context' => ['groups' => ['User:create']],
            ],
        ),
        new Get(
            security: "is_granted('ROLE_ADMIN') or object == user",
            securityMessage: "You can only access your own user"
        ),
        new Put(
            processor: UserPasswordHasher::class,
            security: "is_granted('ROLE_ADMIN') or object == user",
            securityMessage: "You can only access your own user",
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object == user",
        ),
    ],
    normalizationContext: ['groups' => ['User:read']],
    denormalizationContext: ['groups' => ['User:create', 'User:update']],
)]
#[ApiFilter(SearchFilter::class, properties: ['email' => 'exact', 'id' => 'exact'])]
#[ApiFilter(GroupFilter::class, arguments: ['parameterName' => 'groups','overrideDefaultGroups' => false])]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, UUIDEntityInterface
{
    use UUIDEntityTrait;

    use TimeStampableTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[Groups(['User:read', "read:item", "read:collection"])]
    private $id;

    public function __construct()
    {
        $this->generateUUId();
        $this->childrens = new ArrayCollection();
        $this->changes = new ArrayCollection();
        $this->repas = new ArrayCollection();
    }

    #[Assert\NotBlank]
    #[Assert\Email]
    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['User:read', 'User:create', 'User:update'])]
    private ?string $email = null;

    #[Groups(['User:read'])]
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(groups: ['User:create'])]
    #[Groups(['User:create', 'User:update'])]
    //#[Assert\Length(min: 6, max: 40)]
    //#[Assert\Regex(pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/', message: 'Password must contain at least one uppercase letter, one lowercase letter and one number')]
    private ?string $plainPassword = null;

    #[ORM\Column]
    #[Groups(['User:create', 'User:update'])]
    private ?bool $isNounou = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['User:create', 'User:update'])]
    private ?string $phone = null;

    #[ORM\Column(length: 255)]
    #[Groups(['User:create', 'User:update', "User:read"])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['User:create', 'User:update', "User:read"])]
    private ?string $lastName = null;

    #[ORM\ManyToMany(targetEntity: Children::class, inversedBy: 'users')]
    #[Groups(['childrens', "details", "read:item"])]
    private Collection $childrens;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Change::class)]
    #[Groups(['Changes', "details", "read:item"])]
    private Collection $changes;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Repas::class)]
    #[Groups(['repas', "details", "read:item"])]
    private Collection $repas;

    
    #[ORM\Column(length: 255)]
    #[Groups(['User:create', 'User:update', "User:read", "read:item"])]
    private ?string $typeUser = null;

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
     * Méthode getUsername qui permet de retourner le champ qui est utilisé pour l'authentification.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    /**
     * A visual identifier that represents this User.
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
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function addRoles(string $role): array
    {
        if (!in_array($role, $this->roles)) {
            $this->roles[] = $role;
        }

        return $this->getRoles();
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_unique($roles);

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
        // If you store any temporary, sensitive data on the User, clear it here
        $this->plainPassword = null;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function isIsNounou(): ?bool
    {
        return $this->isNounou;
    }

    public function setIsNounou(bool $isNounou): self
    {
        $this->isNounou = $isNounou;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
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

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Children>
     */
    public function getChildrens(): Collection
    {
        return $this->childrens;
    }

    public function addChildren(Children $children): self
    {
        if (!$this->childrens->contains($children)) {
            $this->childrens->add($children);
        }

        return $this;
    }

    public function removeChildren(Children $children): self
    {
        $this->childrens->removeElement($children);

        return $this;
    }

    /**
     * @return Collection<int, Change>
     */
    public function getChanges(): Collection
    {
        return $this->changes;
    }

    public function addChange(Change $change): self
    {
        if (!$this->changes->contains($change)) {
            $this->changes->add($change);
            $change->setOwner($this);
        }

        return $this;
    }

    public function removeChange(Change $change): self
    {
        if ($this->changes->removeElement($change)) {
            // set the owning side to null (unless already changed)
            if ($change->getOwner() === $this) {
                $change->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Repas>
     */
    public function getRepas(): Collection
    {
        return $this->repas;
    }

    public function addRepa(Repas $repa): self
    {
        if (!$this->repas->contains($repa)) {
            $this->repas->add($repa);
            $repa->setOwner($this);
        }

        return $this;
    }

    public function removeRepa(Repas $repa): self
    {
        if ($this->repas->removeElement($repa)) {
            // set the owning side to null (unless already changed)
            if ($repa->getOwner() === $this) {
                $repa->setOwner(null);
            }
        }

        return $this;
    }

    #[ORM\PrePersist]
    public function createRoles()
    {
        if ($this->isNounou) {
            $this->addRoles('ROLE_NOUNOU');
        } else {
            $this->addRoles('ROLE_PARENTS');
        }
        $this->addRoles('ROLE_ADMIN');
    }

    #[Groups(['User:read'])]
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    #[Groups(['User:read'])]
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getTypeUser(): ?string
    {
        return $this->typeUser;
    }

    public function setTypeUser(?string $typeUser): self
    {
        $this->typeUser = $typeUser;

        return $this;
    }

}
