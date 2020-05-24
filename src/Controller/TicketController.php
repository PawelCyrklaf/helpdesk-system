<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Event\TicketClosedEvent;
use App\Event\TicketCreatedEvent;
use App\Service\TicketService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

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
     * @SWG\Post(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Ticket"},
     *     summary="Add new ticket",
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         type="string",
     *         default="Bearer TOKEN",
     *         description="Authorization"
     *     ),
     *     @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       description="JSON ticket object",
     *       type="json",
     *       required=true,
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="subject", type="string", example="Example subject"),
     *              @SWG\Property(property="description", type="string", example="Example ticket description"),
     *          )
     *)
     * )
     * @SWG\Response(
     *         response=200,
     *         description="Returns new ticket id",
     *     @SWG\Schema(
     *     @SWG\Property(property="ticket_id", type="integer", example="1"),
     * )
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Expired JWT Token | JWT Token not found | Invalid JWT Token",
     *     )
     */
    public function add(Request $request)
    {
        $result = $this->ticketService->add($request, $this->getUser());
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
     * @SWG\Put(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     summary="Update existing ticket",
     *     tags={"Ticket"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         type="string",
     *         default="Bearer TOKEN",
     *         description="Authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="Ticket id"
     *     ),
     *     @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       description="JSON ticket object",
     *       type="json",
     *       required=true,
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="subject", type="string", example="Example subject"),
     *              @SWG\Property(property="description", type="string", example="Example ticket description"),
     *          )
     *)
     * )
     * @SWG\Response(
     *         response=200,
     *         description="Returns success status"
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Expired JWT Token | JWT Token not found | Invalid JWT Token",
     *     )
     * @SWG\Response(
     *         response=404,
     *         description="Ticket not found",
     *     )
     */
    public function update(Ticket $ticket, Request $request)
    {
        $this->denyAccessUnlessGranted('TICKET_EDIT', $ticket);
        $result = $this->ticketService->update($request, $ticket);
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
     * @SWG\Delete(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     summary="Delete existing ticket",
     *     tags={"Ticket"},
     * @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         type="string",
     *         default="Bearer TOKEN",
     *         description="Authorization"
     *     ),
     * @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="Ticket id"
     *     )
     * )
     * @SWG\Response(
     *         response=204,
     *         description="Returns success status"
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Expired JWT Token | JWT Token not found | Invalid JWT Token",
     *     )
     * @SWG\Response(
     *         response=404,
     *         description="Ticket not found",
     *     )
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
     * @SWG\Get(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     summary="Get existing ticket details",
     *     tags={"Ticket"},
     * @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         type="string",
     *         default="Bearer TOKEN",
     *         description="Authorization"
     *     ),
     * @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="Ticket id"
     *     )
     * )
     * @SWG\Response(
     *         response=200,
     *         description="Returns ticket details",
     *          @Model(type=Ticket::class)
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Expired JWT Token | JWT Token not found | Invalid JWT Token",
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="Ticket not found",
     *     )
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
     * @SWG\Get(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     summary="Get tickets list",
     *     tags={"Ticket"},
     * @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         type="string",
     *         default="Bearer TOKEN",
     *         description="Authorization"
     *     )
     * )
     * @SWG\Response(
     *         response=200,
     *         description="Returns tickets list",
     *     @SWG\Schema(
     *     type="array",
     *     @Model(type=Ticket::class)
     * )
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Expired JWT Token | JWT Token not found | Invalid JWT Token",
     *     )
     */
    public function list(Request $request)
    {
        $tickets = $this->ticketService->getTickets($request, $this->getUser());
        return $this->view($tickets, Response::HTTP_OK);
    }

    /**
     * @Rest\Put("/ticket/{id}/close")
     * @IsGranted("ROLE_ADMIN",message="Only administrator can close ticket.")
     * @param Ticket $ticket
     * @return View
     * @SWG\Put(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     summary="Close existing ticket",
     *     tags={"Ticket"},
     *     @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         type="string",
     *         default="Bearer TOKEN",
     *         description="Authorization"
     *     ),
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         type="integer",
     *         description="Ticket id"
     *     ),
     *     @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       description="JSON ticket object",
     *       type="json",
     *       required=true,
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="status", type="integer", example="1"),
     *          )
     *)
     * )
     * @SWG\Response(
     *         response=200,
     *         description="Returns success status"
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Expired JWT Token | JWT Token not found | Invalid JWT Token",
     *     )
     * @SWG\Response(
     *         response=404,
     *         description="Ticket not found",
     *     )
     */
    public function closeTicket(Ticket $ticket)
    {
        $result = $this->ticketService->changeStatus($ticket);
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
