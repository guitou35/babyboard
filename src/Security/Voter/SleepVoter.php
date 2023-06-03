<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SleepVoter extends Voter
{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';
    public const DELETE = 'DELETE';
    public const SLEEP_ADD = 'SLEEP_ADD';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::EDIT, self::VIEW, self::DELETE, self::SLEEP_ADD])
            && $subject instanceof \App\Entity\Sleep;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::SLEEP_ADD:
                if($subject->getChildren()->getUsers()->contains($user))
                    return true;

            case self::DELETE:
            Case self::VIEW:
            case self::EDIT:
                // logic to determine if the user can EDIT
                // return true or false
                if($subject->getOwner() === $user)
                    return true;
                break;
        }

        return false;
    }
}
