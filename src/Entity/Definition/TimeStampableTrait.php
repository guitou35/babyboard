<?php

namespace App\Entity\Definition;

use DateTime;
use DateTimeInterface;
use Exception;
use Doctrine\ORM\Mapping as ORM;


/**
 * Trait TimeStampableTrait
 * @package App\Entity\Definition
 */
trait TimeStampableTrait
{

    #[ORM\Column(type: "datetime")]
    private $createdAt = null;

    #[ORM\Column(type: "datetime")]
    private $updatedAt;

    /**
     * @return DateTimeInterface|null
     * @throws Exception
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface $updatedAt
     * @return $this
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist()]
    public function prePersist(): void
    {
        $now = new DateTime();
        $this->setCreatedAt($now);
        $this->setUpdatedAt($now);
    }

    #[ORM\PreUpdate()]
    public function preUpdate(): void
    {
        $now = new DateTime();
        $this->setUpdatedAt($now);
    }
}