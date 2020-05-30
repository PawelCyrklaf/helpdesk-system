<?php

namespace App\Service;

use App\Entity\Reply;
use App\Entity\Ticket;
use App\Repository\ReplyRepository;
use App\Repository\TicketRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReplyService
{
    private ReplyRepository $replyRepository;
    private ValidatorInterface $validator;
    private ErrorService $errorService;
    private TicketRepository $ticketRepository;

    public function __construct(
        ReplyRepository $replyRepository,
        ValidatorInterface $validator,
        ErrorService $errorService,
        TicketRepository $ticketRepository
    )
    {
        $this->replyRepository = $replyRepository;
        $this->validator = $validator;
        $this->errorService = $errorService;
        $this->ticketRepository = $ticketRepository;
    }

    public function add(Request $request, $author, Ticket $ticket)
    {
        $replyData = json_decode($request->getContent(), true);
        $message = $replyData['message'];

        $reply = new Reply();
        $reply->setMessage($message);
        $reply->setAuthor($author);
        $reply->setTicket($ticket);

        $errors = $this->validator->validate($reply);
        if (count($errors) > 0) {
            return $this->errorService->formatError($errors);
        }

        if (in_array('ROLE_ADMIN', $author->getRoles())) {
            $ticket->setStatus(Ticket::SUPPORT_REPLY);
        } else {
            $ticket->setStatus(Ticket::CUSTOMER_REPLY);
        }

        $this->ticketRepository->update();
        $this->replyRepository->save($reply);
        return $reply;
    }

    public function update(Request $request, Reply $reply): Reply
    {
        $replyData = json_decode($request->getContent(), true);
        $message = $replyData['message'];

        if (!$message) {
            throw new BadRequestHttpException('Message cannot be empty!');
        }

        $reply->setMessage($message);
        $this->replyRepository->update();

        return $reply;
    }

    public function remove(Reply $reply): bool
    {
        $this->replyRepository->remove($reply);
        return true;
    }
}