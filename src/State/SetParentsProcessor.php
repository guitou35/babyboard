<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Children;

class SetParentsProcessor implements ProcessorInterface
{
    public function __construct(private readonly ProcessorInterface $processor, private readonly Security $security)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();
        if($data instanceOf Children) {
            $data->addUser($user);
            $data->addParents($user->getId(), $user->getTypeUser());
        }
        
        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
