<?php

namespace App\Service;

use App\Entity\Reply;
use App\Entity\Ticket;
use App\Repository\ReplyRepository;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReplyService
{
    private ReplyRepository $replyRepository;
    private ValidatorInterface $validator;

    public function __construct(
        ReplyRepository $replyRepository,
        ValidatorInterface $validator
    )
    {
        $this->replyRepository = $replyRepository;
        $this->validator = $validator;
    }

    public function add(array $replyData, $author, Ticket $ticket)
    {
        $message = $replyData['message'];

        $reply = new Reply();
        $reply->setMessage($message);
        $reply->setAuthor($author);
        $reply->setTicket($ticket);

        $errors = $this->validator->validate($reply);
        if (count($errors) > 0) {
            return (string)$errors;
        }

        $this->replyRepository->save($reply);
        return $reply;
    }

    public function update(array $replyData, Reply $reply): Reply
    {
        $message = $replyData['message'];

        $reply->setMessage($message);
        $this->replyRepository->update();

        return $reply;
    }
}