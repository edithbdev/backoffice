<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class AdminVoter extends Voter
{
    protected function supports(string $attribute, $subject): bool
    {
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['POST_READ, POST_NEW', 'POST_EDIT', 'POST_DELETE'])
            && $subject instanceof \App\Entity\Project
            || $subject instanceof \App\Entity\User;

    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case 'POST_READ':
                if (\in_array('ROLE_ADMIN', $user->getRoles())) {
                    return true;
                }
                break;
            case 'POST_NEW':
                if (\in_array('ROLE_ADMIN', $user->getRoles())) {
                    return true;
                }
                break;
            case 'POST_EDIT':
                if (\in_array('ROLE_ADMIN', $user->getRoles())) {
                    return true;
                }
                break;
            case 'POST_DELETE':
                if (\in_array('ROLE_ADMIN', $user->getRoles())) {
                    return true;
                }
                break;

            default:
                return false;
        }

        return false;
    }
}


        // if ($attribute) {
        //         if ($user->getRoles()[0] === 'ROLE_ADMIN') {
        //             return true;
        //         }
        //         return false;
        // }


