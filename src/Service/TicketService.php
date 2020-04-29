<?php

namespace App\Service;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TicketService
{
    /** @var TicketRepository */
    private $ticketRepository;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var ValidatorInterface */
    private $validator;

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

    public function add(array $ticketData, $author): string
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


    public function update($ticketData, int $ticketId): Ticket
    {
        $subject = $ticketData['subject'];
        $description = $ticketData['description'];

        $ticket = $this->ticketRepository->find($ticketId);

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket with id ' . $ticketId . ' does not exist!');
        }

        $ticket->setSubject($subject);
        $ticket->setDescription($description);

        $this->entityManager->flush();
        return $ticket;
    }


    public function remove(int $ticketId): bool
    {
        $ticket = $this->ticketRepository->find($ticketId);

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket with id ' . $ticketId . ' does not exist!');
        }

        $this->entityManager->remove($ticket);
        $this->entityManager->flush();
        return true;
    }

    public function getTicket(int $ticketId): ?Ticket
    {
        $ticket = $this->ticketRepository->find($ticketId);

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket with id ' . $ticketId . ' does not exist!');
        }
        return $ticket;
    }


    public function getTickets(): array
    {
        return $this->ticketRepository->findAll();
    }

    public function changeStatus($ticketData, $ticketId): Ticket
    {
        $ticket = $this->ticketRepository->find($ticketId);
        $status = $ticketData['status'];

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket with id ' . $ticketId . ' does not exist!');
        }

        $ticket->setStatus($status);

        $this->entityManager->flush();
        return $ticket;
    }
}