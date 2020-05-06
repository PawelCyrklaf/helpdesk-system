<?php

namespace App\Controller;

use App\Entity\Reply;
use App\Entity\Ticket;
use App\Event\TicketReplyEvent;
use App\Service\ReplyService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReplyController extends AbstractFOSRestController
{
    private ReplyService $replyService;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        ReplyService $replyService,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->replyService = $replyService;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Rest\Post("/ticket/{id}/reply")
     * @param Request $request
     * @param Ticket $ticket
     * @return View
     */
    public function add(Request $request, Ticket $ticket)
    {
        $this->denyAccessUnlessGranted('TICKET_ADD_REPLY', $ticket);
        if ($request->getContent() == null) {
            throw new BadRequestHttpException('Request body cannot be null');
        }

        $replyData = json_decode($request->getContent(), true);
        $user = $this->getUser();
        $result = $this->replyService->add($replyData, $user, $ticket);

        if ($result instanceof Reply) {
            $replyEvent = new TicketReplyEvent($result->getTicket());
            $this->dispatcher->dispatch($replyEvent, TicketReplyEvent::class);

            return $this->view(['reply_id' => $result->getId()], Response::HTTP_OK);
        }
        return $this->view(['error' => $result], Response::HTTP_BAD_REQUEST);

    }

    /**
     * @Rest\Put("/reply/{id}")
     * @param Request $request
     * @param Reply $reply
     * @return View
     */
    public function update(Request $request, Reply $reply)
    {
        $this->denyAccessUnlessGranted('REPLY_EDIT', $reply);
        if ($request->getContent() == null) {
            throw new BadRequestHttpException('Request body cannot be null');
        }

        $replyData = json_decode($request->getContent(), true);
        $result = $this->replyService->update($replyData, $reply);

        if ($result) {
            return $this->view([], Response::HTTP_OK);
        }
        return $this->view(['error' => $result], Response::HTTP_BAD_REQUEST);
    }
}
