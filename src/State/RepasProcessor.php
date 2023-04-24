<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Repas;
use ApiPlatform\Metadata\Post;

class RepasProcessor implements ProcessorInterface
{
    public function __construct(private readonly ProcessorInterface $processor, private readonly Security $security)
    {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        $user = $this->security->getUser();

        if(!$data->getChildren()->getUsers()->contains($user)) {
            throw new \Exception('You can only access your repas for your children');
        }
        
        if($data instanceof Repas && $operation instanceof Post) {
            $data->setOwner($user);
        }

        

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
