<?php

namespace App\Service;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TicketService
{
    private TicketRepository $ticketRepository;
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(
        TicketRepository $ticketRepository,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    )
    {
        $this->ticketRepository = $ticketRepository;
        $this->entityManager = $entityManager;
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

        $this->entityManager->persist($ticket);
        $this->entityManager->flush();
        return $ticket;
    }

    public function update(array $ticketData, Ticket $ticket): Ticket
    {
        $subject = $ticketData['subject'];
        $description = $ticketData['description'];

        $ticket->setSubject($subject);
        $ticket->setDescription($description);

        $this->entityManager->flush();
        return $ticket;
    }

    public function remove(Ticket $ticket): bool
    {
        $this->entityManager->remove($ticket);
        $this->entityManager->flush();
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

        $this->entityManager->flush();
        return $ticket;
    }
}