<?php

namespace App\Service;

use App\Entity\Ticket;
use App\Repository\TicketRepository;
use App\Utils\ErrorFormatter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TicketService
{
    private TicketRepository $ticketRepository;
    private ValidatorInterface $validator;
    private PaginatorInterface $paginator;
    private ErrorFormatter $errorFormatter;

    public function __construct(
        TicketRepository $ticketRepository,
        ValidatorInterface $validator,
        PaginatorInterface $paginator,
        ErrorFormatter $errorFormatter
    )
    {
        $this->ticketRepository = $ticketRepository;
        $this->validator = $validator;
        $this->paginator = $paginator;
        $this->errorFormatter = $errorFormatter;
    }

    public function add(Request $request, $author)
    {
        $ticketData = json_decode($request->getContent(), true);
        $subject = $ticketData['subject'];
        $description = $ticketData['description'];

        $ticket = new Ticket();
        $ticket->setSubject($subject);
        $ticket->setDescription($description);
        $ticket->setAuthor($author);
        $ticket->setStatus(Ticket::UNRESOLVED);

        $errors = $this->validator->validate($ticket);
        if (count($errors) > 0) {
            return $this->errorFormatter->formatError($errors);
        }

        $this->ticketRepository->save($ticket);
        return $ticket;
    }

    public function update(Request $request, Ticket $ticket): Ticket
    {
        $ticketData = json_decode($request->getContent(), true);
        $subject = $ticketData['subject'];
        $description = $ticketData['description'];

        if (!$subject) {
            throw new BadRequestHttpException('Subject cannot be empty!');
        }
        $ticket->setSubject($subject);

        if (!$description) {
            throw new BadRequestHttpException('Description cannot be empty!');
        }
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

    public function changeStatus(Ticket $ticket): Ticket
    {
        $ticket->setStatus(Ticket::SOLVED);
        $this->ticketRepository->update();
        return $ticket;
    }
}