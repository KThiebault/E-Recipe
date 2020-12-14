<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OwnerVoter extends Voter
{
    private const OWNER = "OWNER";

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::OWNER]) && $subject instanceof Category || $subject instanceof Ingredient;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return $subject->getUser() === $user;
    }
}
