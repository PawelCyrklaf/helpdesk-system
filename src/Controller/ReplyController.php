<?php

namespace App\Controller;

use App\Entity\Reply;
use App\Entity\Ticket;
use App\Event\TicketReplyEvent;
use App\Service\ReplyService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

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
     * @SWG\Post(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Reply"},
     *     summary="Add new reply",
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
     *         type="string",
     *         description="Ticket id"
     *     ),
     *     @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       description="JSON reply object",
     *       type="json",
     *       required=true,
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="message", type="string", example="This is example message"),
     *          )
     *)
     * )
     * @SWG\Response(
     *         response=200,
     *         description="Returns new reply id",
     *     @SWG\Schema(
     *     @SWG\Property(property="reply_id", type="integer", example="1"),
     * )
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Expired JWT Token | JWT Token not found | Invalid JWT Token",
     *     )
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
     * @SWG\Put(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Reply"},
     *     summary="Update existing reply",
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
     *         type="string",
     *         description="Reply id"
     *     ),
     *     @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       description="JSON reply object",
     *       type="json",
     *       required=true,
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="message", type="string", example="This is example message"),
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
     */
    public function update(Request $request, Reply $reply)
    {
        $result = $this->replyService->update($request, $reply);

        if ($result) {
            return $this->view([], Response::HTTP_OK);
        }
        return $this->view(['error' => $result], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/reply/{id}")
     * @IsGranted("ROLE_ADMIN",message="Only administrator can remove reply.")
     * @param Reply $reply
     * @return bool|View
     * @SWG\Delete(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     summary="Delete existing reply",
     *     tags={"Reply"},
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
     *         description="Reply id"
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
     *         description="Reply not found",
     *     )
     */
    public function remove(Reply $reply)
    {
        $result = $this->replyService->remove($reply);
        if ($result) {
            return $this->view([], Response::HTTP_NO_CONTENT);
        }
        return false;
    }
}
