<?php

namespace App\Service;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TicketService
{
    private TicketRepository $ticketRepository;
    private ValidatorInterface $validator;

    public function __construct(
        TicketRepository $ticketRepository,
        ValidatorInterface $validator
    )
    {
        $this->ticketRepository = $ticketRepository;
        $this->validator = $validator;
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

    public function getTickets(): array
    {
        return $this->ticketRepository->findAll();
    }

    public function changeStatus(array $ticketData, Ticket $ticket): Ticket
    {
        $status = $ticketData['status'];
        $ticket->setStatus($status);

        $this->ticketRepository->update();
        return $ticket;
    }
}