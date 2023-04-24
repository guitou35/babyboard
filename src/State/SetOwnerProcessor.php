<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Change;
use App\Entity\Repas;

class SetOwnerProcessor implements ProcessorInterface
{
    public function __construct(private readonly ProcessorInterface $processor, private readonly Security $security)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();
        if (($data instanceof Change || $data instanceof Repas) && $data->getOwner() === null) {
            $data->setOwner($user);
        }
        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
