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

#[ORM\Entity(repositoryClass: SleepRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            openapiContext: [
                'summary' => 'Get all sleeps',
                'description' => 'Get all sleeps',
            ],
        ),
        new Get(
            openapiContext: [
                'summary' => 'Get a sleep',
                'description' => 'Get a sleep',
            ],
        ),
        new Post(),
        new Put(),
        new Delete(),
    ], 
)]
class Sleep
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $endAt = null;

    #[ORM\Column(type: Types::BIGINT, nullable: true)]
    private ?string $timeSleep = null;

    #[ORM\ManyToOne(inversedBy: 'sleeps')]
    private ?Children $children = null;

    #[ORM\ManyToOne]
    private ?User $owner = null;

    public function getId(): ?int
    {
        return $this->id;
    }

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
