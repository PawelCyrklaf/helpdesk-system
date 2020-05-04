<?php

namespace App\Service;

use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\TicketRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TicketService
{
    private TicketRepository $ticketRepository;
    private ValidatorInterface $validator;
    private PaginatorInterface $paginator;

    public function __construct(
        TicketRepository $ticketRepository,
        ValidatorInterface $validator,
        PaginatorInterface $paginator
    )
    {
        $this->ticketRepository = $ticketRepository;
        $this->validator = $validator;
        $this->paginator = $paginator;
    }

    public function add(array $ticketData, $author)
    {
        $subject = $ticketData['subject'];
        $description = $ticketData['description'];

        $ticket = new Ticket();
        $ticket->setSubject($subject);
        $ticket->setDescription($description);
        $ticket->setAuthor($author);
        $ticket->setStatus(Ticket::UNRESOLVED);

        $errors = $this->validator->validate($ticket);

        if (count($errors) > 0) {
            return (string)$errors;
        }

        $this->ticketRepository->save($ticket);
        return $ticket;
    }

    public function update(array $ticketData, Ticket $ticket): Ticket
    {
        $subject = $ticketData['subject'];
        $description = $ticketData['description'];

        $ticket->setSubject($subject);
        $ticket->setDescription($description);

        $this->ticketRepository->update();
        return $ticket;
    }

    public function remove(Ticket $ticket): bool
    {
        $this->ticketRepository->remove($ticket);
        return true;
    }

    public function getTickets(Request $request, UserInterface $user): iterable
    {
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $tickets = $this->ticketRepository->findAll();
        } else {
            $tickets = $this->ticketRepository->findBy(array('author' => $user));
        }
        return $this->paginator->paginate($tickets, $request->query->getInt('page', 1), $request->query->getInt('limit', 10))->getItems();
    }

    public function changeStatus(array $ticketData, Ticket $ticket): Ticket
    {
        $status = $ticketData['status'];
        $ticket->setStatus($status);

        $this->ticketRepository->update();
        return $ticket;
    }
}