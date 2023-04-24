<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Serializer\Filter\GroupFilter;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Link;
use App\Entity\Definition\TimeStampableTrait;
use App\Entity\Definition\UUIDEntityTrait;
use App\State\SetParentsProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use App\State\GetOwnerProvider;

#[ORM\Entity()]
#[ORM\Table(name: '`children`')]
#[ApiResource(
    operations: [
        new Get(
            security: "is_granted('VIEW', object)",
            securityMessage: "You can only access your own user"
        ),
        new GetCollection(
            security: " is_granted('ROLE_ADMIN')",
            provider: GetOwnerProvider::class,
            uriTemplate: '/users/{userId}/childrens',
            uriVariables: [
                'userId' => 
                new Link(fromClass: User::class, fromProperty: 'id', toClass: Children::class, toProperty: 'users')],
            openapiContext: [
                'summary' => 'Get all childrens of a user',
                'description' => 'Get all childrens of a user',
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
            processor: SetParentsProcessor::class,
        ),
        new Put(
            security: "is_granted('EDIT', object)",
            securityMessage: "You can only access your own user",
            processor: SetParentsProcessor::class,
        ),
        new Delete(
            security: "is_granted('DELETE', object)",
            securityMessage: "You can only delete your own user",
        )
    ],
    normalizationContext: ['groups' => ['Children:read']],
    denormalizationContext: ['groups' => ['Children:create', 'Children:update']]
)]
#[ORM\HasLifecycleCallbacks]
#[ApiFilter(GroupFilter::class, arguments: ['parameterName' => 'groups','overrideDefaultGroups' => false])]
class Children
{

    use UUIDEntityTrait;

    use TimeStampableTrait;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: 'uuid', unique: true)]
    #[Groups(["Children:read", "read:item"])]
    private $id;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["Children:read", "Children:create", "Children:update", "read:item"])]
    private $name;

    #[ORM\Column(type:'date')]
    #[Groups(["Children:read", "Children:create", "read:item"])]
    private $birthdate;

    #[ORM\Column(type:'float')]
    #[Groups(["Children:read", "Children:create", "read:item"])]
    private $weight;

    #[ORM\Column(type:'float')]
    #[Groups(["Children:read", "Children:create", "read:item"])]
    private $size;

    #[ORM\OneToMany(mappedBy: 'children', targetEntity: Repas::class, orphanRemoval: true)]
    private Collection $repas;

    #[ORM\OneToMany(mappedBy: 'children', targetEntity: Change::class, orphanRemoval: true)]
    private Collection $changes;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'childrens')]
    #[Groups(["Children:read"])]
    private Collection $users;

    #[ORM\Column(type: 'json')]
    #[Groups(["Children:read"])]
    private array $parents = [];

    #[ORM\Column(type: 'json')]
    #[Groups(["Children:read", "Children:create", "Children:update"])]
    private array $nounou = [];

    #[ORM\OneToMany(mappedBy: 'children', targetEntity: Sleep::class)]
    private Collection $sleeps;


    public function __construct()
    {
        $this->generateUUId();
        $this->repas = new ArrayCollection();
        $this->changes = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->sleeps = new ArrayCollection();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getBirthdate(): \DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): self
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getSize(): float
    {
        return $this->size;
    }

    public function setSize(float $size): self
    {
        $this->size = $size;

        return $this;
    }



    /**
     * @return Collection<int, Repas>
     */
    public function getrepas(): Collection
    {
        return $this->repas;
    }

    public function addRepa(Repas $repa): self
    {
        if (!$this->repas->contains($repa)) {
            $this->repas->add($repa);
            $repa->setChildren($this);
        }

        return $this;
    }

    public function removeRepa(Repas $repa): self
    {
        if ($this->repas->removeElement($repa)) {
            // set the owning side to null (unless already changed)
            if ($repa->getChildren() === $this) {
                $repa->setChildren(null);
            }
        }

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
            $change->setChildren($this);
        }

        return $this;
    }

    public function removeChange(Change $change): self
    {
        if ($this->changes->removeElement($change)) {
            // set the owning side to null (unless already changed)
            if ($change->getChildren() === $this) {
                $change->setChildren(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addChildren($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeChildren($this);
        }

        return $this;
    }

    public function getParents(): array
    {
        return $this->parents;
    }

    public function setParents(array $parents): self
    {
        $this->parents = $parents;

        return $this;
    }

    public function addParents(Uuid $parent, $key): self
    {
        $famille = ["papa", "maman", "grandpapa", "grandmaman", "autre"];
        if(((array_key_exists($key, $this->parents) && $this->parents[$key] !== $parent) | !array_key_exists($key, $this->parents)) && in_array($key, $famille)){
            $this->parents[$key] = $parent;
        }

        return $this;
    }

    /**
     * @return Collection<int, Sleep>
     */
    public function getSleeps(): Collection
    {
        return $this->sleeps;
    }

    public function addSleep(Sleep $sleep): self
    {
        if (!$this->sleeps->contains($sleep)) {
            $this->sleeps->add($sleep);
            $sleep->setChildren($this);
        }
        return $this;
    }

    public function removeSleep(Sleep $sleep): self
    {
        if ($this->sleeps->removeElement($sleep)) {
            // set the owning side to null (unless already changed)
            if ($sleep->getChildren() === $this) {
                $sleep->setChildren(null);
            }
        }
        return $this;
    }


}
