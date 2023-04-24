<?php

namespace App\State;

use ApiPlatform\State\ProviderInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Children;
use App\Exception\HttpNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\SecurityBundle\Security;

final class GetOwnerProvider implements ProviderInterface
{

    public function __construct(private Security $security, private ProviderInterface $itemProvider)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        /** @var UserInterface $user */
        $user = $this->security->getUser();

        if(!$user instanceof UserInterface && !$user->getId() == $uriVariables['userId']){
            return null;
        }

        $result = $this->itemProvider->provide($operation, $uriVariables, $context);

        if($result == null){
            throw new HttpNotFoundException(sprintf('Entity not found'));
        }
        
        return $result;
    }
}