<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Service\TicketService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class TicketController extends AbstractFOSRestController
{
    /** @var TicketService */
    private $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
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
            return $this->view(['ticket_id' => $result->getId()], Response::HTTP_OK);
        }
        return $this->view(['error' => $result], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/ticket/{ticketId}")
     * @param Request $request
     * @param int $ticketId
     * @return View
     */
    public function update(Request $request, int $ticketId)
    {
        if ($request->getContent() == null) {
            throw new BadRequestHttpException('Request body cannot be null');
        }

        $ticketData = json_decode($request->getContent(), true);
        $result = $this->ticketService->update($ticketData, $ticketId);

        if ($result) {
            return $this->view([], Response::HTTP_OK);
        }
        return $this->view(['error' => $result], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/ticket/{ticketId}")
     * @param int $ticketId
     * @return bool|View
     */
    public function remove(int $ticketId)
    {
        $result = $this->ticketService->remove($ticketId);

        if ($result) {
            return $this->view([], Response::HTTP_NO_CONTENT);
        }
        return false;
    }

    /**
     * @Rest\Get("/ticket/{ticketId}")
     * @param int $ticketId
     * @return View
     */
    public function details(int $ticketId)
    {
        $tickets = $this->ticketService->getTicket($ticketId);
        return $this->view($tickets, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/tickets")
     */
    public function list()
    {
        $tickets = $this->ticketService->getTickets();
        return $this->view($tickets, Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/ticket/{ticketId}/status")
     * @param Request $request
     * @param int $ticketId
     * @return View
     */
    public function changeStatus(Request $request, int $ticketId)
    {
        if ($request->getContent() == null) {
            throw new BadRequestHttpException('Request body cannot be null');
        }

        $ticketData = json_decode($request->getContent(), true);
        $result = $this->ticketService->changeStatus($ticketData, $ticketId);

        if ($result) {
            return $this->view([], Response::HTTP_OK);
        }
        return $this->view(['error' => $result], Response::HTTP_BAD_REQUEST);
    }
}
