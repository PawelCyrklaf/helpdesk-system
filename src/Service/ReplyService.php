<?php

namespace App\Service;

use App\Entity\Reply;
use App\Entity\Ticket;
use App\Repository\ReplyRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReplyService
{
    private ReplyRepository $replyRepository;
    private ValidatorInterface $validator;
    private ErrorService $errorService;

    public function __construct(
        ReplyRepository $replyRepository,
        ValidatorInterface $validator,
        ErrorService $errorService
    )
    {
        $this->replyRepository = $replyRepository;
        $this->validator = $validator;
        $this->errorService = $errorService;
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

        $this->replyRepository->save($reply);
        return $reply;
    }

    public function update(Request $request, Reply $reply): Reply
    {
        $replyData = json_decode($request->getContent(), true);
        $message = $replyData['message'];
        $reply->setMessage($message);
        $this->replyRepository->update();

        return $reply;
    }
}