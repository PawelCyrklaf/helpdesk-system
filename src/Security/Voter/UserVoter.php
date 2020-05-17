<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['USER_EDIT', 'USER_VIEW'])
            && $subject instanceof User;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }
        $userData = $subject;
        switch ($attribute) {
            case 'USER_EDIT':
                return $this->canEdit($user, $userData);
                break;
            case 'USER_VIEW':
                return $this->canView($user, $userData);
                break;
        }

        return false;
    }

    public function canEdit(User $user, User $userData)
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        } else {
            return $user === $userData;
        }
    }

    public function canView(User $user, User $userData)
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        } else {
            return $user === $userData;
        }
    }
}
