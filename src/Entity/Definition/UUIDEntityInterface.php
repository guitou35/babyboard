<?php

namespace App\Entity\Definition;

use Symfony\Component\Uid\Uuid;

interface UUIDEntityInterface
{

    /**
     * @return Uuid
     */
    public function getId();
}