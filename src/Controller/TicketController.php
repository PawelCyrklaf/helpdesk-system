<?php

namespace App\Controller;

use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Swagger\Annotations as SWG;


class TicketController extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/ticket")
     * @SWG\Response(
     *     response=200,
     *     description="Returns new ticket id",
     * @SWG\Items(
     *              type="object",
     *              @SWG\Property(property="ticket_id", type="integer", description="Ticket ID"),
     *          )
     * )
     * @SWG\Parameter(
     *        name="body",
     *        in="body",
     *        description="ticket request object",
     *        required=true,
     *        @SWG\Schema(
     *          type="object",
     *          @Model(type=Ticket::class)
     *        )
     *      )
     * @SWG\Tag(name="Ticket");
     * @Security(name="Bearer")
     * @param Request $request
     * @return View
     */
    public function add(Request $request)
    {
        if ($request->getContent() == null) {
            throw new BadRequestHttpException('Request body cannot be null');
        }

        $ticketData = json_decode($request->getContent(), true);
        $result = $this->ticketService->add($ticketData);

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
