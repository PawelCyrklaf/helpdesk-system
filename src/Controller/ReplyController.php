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
        $result = $this->replyService->add($request, $this->getUser(), $ticket);

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
        $result = $this->replyService->update($request, $reply);

        if ($result) {
            return $this->view([], Response::HTTP_OK);
        }
        return $this->view(['error' => $result], Response::HTTP_BAD_REQUEST);
    }
}
