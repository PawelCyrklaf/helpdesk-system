<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Event\TicketClosedEvent;
use App\Event\TicketCreatedEvent;
use App\Service\TicketService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TicketController extends AbstractFOSRestController
{
    private TicketService $ticketService;

    private EventDispatcherInterface $dispatcher;

    public function __construct(
        TicketService $ticketService,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->ticketService = $ticketService;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Rest\Post("/ticket")
     * @param Request $request
     * @return View
     */
    public function add(Request $request)
    {
        if ($request->getContent() == null) {
            throw new BadRequestHttpException('Request body cannot be null');
        }

        $ticketData = json_decode($request->getContent(), true);
        $user = $this->getUser();
        $result = $this->ticketService->add($ticketData, $user);

        if ($result instanceof Ticket) {
            $ticketEvent = new TicketCreatedEvent($result);
            $this->dispatcher->dispatch($ticketEvent, TicketCreatedEvent::class);

            return $this->view(['ticket_id' => $result->getId()], Response::HTTP_OK);
        }
        return $this->view(['error' => $result], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/ticket/{id}")
     * @param Request $request
     * @param Ticket $ticket
     * @return View
     */
    public function update(Ticket $ticket, Request $request)
    {
        $this->denyAccessUnlessGranted('TICKET_EDIT', $ticket);
        if ($request->getContent() == null) {
            throw new BadRequestHttpException('Request body cannot be null');
        }

        $ticketData = json_decode($request->getContent(), true);
        $result = $this->ticketService->update($ticketData, $ticket);

        if ($result) {
            return $this->view([], Response::HTTP_OK);
        }
        return $this->view(['error' => $result], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/ticket/{id}")
     * @IsGranted("ROLE_ADMIN",message="Only administrator can remove ticket.")
     * @param Ticket $ticket
     * @return bool|View
     */
    public function remove(Ticket $ticket)
    {
        $result = $this->ticketService->remove($ticket);

        if ($result) {
            return $this->view([], Response::HTTP_NO_CONTENT);
        }
        return false;
    }

    /**
     * @Rest\Get("/ticket/{id}")
     * @param Ticket $ticket
     * @return View
     */
    public function details(Ticket $ticket)
    {
        $this->denyAccessUnlessGranted('TICKET_VIEW', $ticket);
        return $this->view($ticket, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/tickets")
     * @param Request $request
     * @return View
     */
    public function list(Request $request)
    {
        $tickets = $this->ticketService->getTickets($request, $this->getUser());
        return $this->view($tickets, Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/ticket/{id}/close")
     * @IsGranted("ROLE_ADMIN",message="Only administrator can close ticket.")
     * @param Request $request
     * @param Ticket $ticket
     * @return View
     */
    public function closeTicket(Request $request, Ticket $ticket)
    {
        if ($request->getContent() == null) {
            throw new BadRequestHttpException('Request body cannot be null');
        }

        $ticketData = json_decode($request->getContent(), true);
        $result = $this->ticketService->changeStatus($ticketData, $ticket);

        if ($result) {
            $status = $result->getStatus();

            if ($status === Ticket::SOLVED) {
                $ticketEvent = new TicketClosedEvent($result);
                $this->dispatcher->dispatch($ticketEvent, TicketClosedEvent::class);
            }

            return $this->view([], Response::HTTP_OK);
        }
        return $this->view(['error' => $result], Response::HTTP_BAD_REQUEST);
    }

}
