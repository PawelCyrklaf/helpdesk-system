<?php

namespace App\Service;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Exception;
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

        try {
            $this->entityManager->persist($ticket);
            $this->entityManager->flush();
            return $ticket;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function update($ticketData, int $ticketId)
    {
        $subject = $ticketData['subject'];
        $description = $ticketData['description'];

        $ticket = $this->ticketRepository->find($ticketId);

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket with id ' . $ticketId . ' does not exist!');
        }

        $ticket->setSubject($subject);
        $ticket->setDescription($description);

        try {
            $this->entityManager->flush();
            return $ticket;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function remove(int $ticketId)
    {
        $ticket = $this->ticketRepository->find($ticketId);

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket with id ' . $ticketId . ' does not exist!');
        }

        try {
            $this->entityManager->remove($ticket);
            $this->entityManager->flush();
            return true;
        } catch (ORMException $e) {
            return false;
        }
    }

    public function getTicket(int $ticketId)
    {
        $ticket = $this->ticketRepository->find($ticketId);

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket with id ' . $ticketId . ' does not exist!');
        }
        return $ticket;
    }

    public function getTickets()
    {
        return $this->ticketRepository->findAll();
    }

    public function changeStatus($ticketData, $ticketId)
    {
        $ticket = $this->ticketRepository->find($ticketId);
        $status = $ticketData['status'];

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket with id ' . $ticketId . ' does not exist!');
        }

        $ticket->setStatus($status);

        try {
            $this->entityManager->flush();
            return $ticket;
        } catch (Exception $ex) {
            return $ex->getMessage();
        }
    }
}