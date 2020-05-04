<?php

namespace App\Security\Voter;

use App\Entity\Ticket;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TicketVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        return in_array($attribute, ['TICKET_EDIT', 'TICKET_VIEW'])
            && $subject instanceof Ticket;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        $ticket = $subject;

        switch ($attribute) {
            case 'TICKET_EDIT':
                return $this->canEdit($ticket, $user);
                break;
            case 'TICKET_VIEW':
                return $this->canView($ticket, $user);
                break;
        }

        return false;
    }

    public function canEdit(Ticket $ticket, User $user)
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        } else {
            return $user === $ticket->getAuthor();
        }
    }

    public function canView(Ticket $ticket, User $user)
    {
        if (in_array("ROLE_ADMIN", $user->getRoles())) {
            return true;
        } else {
            return $user === $ticket->getAuthor();
        }
    }
}
