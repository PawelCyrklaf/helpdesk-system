<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\TicketService;
use App\Service\UserService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Swagger\Annotations as SWG;

class UserController extends AbstractFOSRestController
{
    private UserService $userService;
    private TicketService $ticketService;

    public function __construct(
        UserService $userService,
        TicketService $ticketService)
    {
        $this->userService = $userService;
        $this->ticketService = $ticketService;
    }

    /**
     * @Rest\Post("/user")
     * @param Request $request
     * @return View
     * @SWG\Post(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"User"},
     *     summary="Add new user",
     *     @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       description="JSON user object",
     *       type="json",
     *       required=true,
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="name", type="string", example="Lorem"),
     *              @SWG\Property(property="surname", type="string", example="Ipsum"),
     *              @SWG\Property(property="email", type="string", example="lorem.ipsum@example.com"),
     *              @SWG\Property(property="password", type="string", example="Lorem1234"),
     *              @SWG\Property(property="phoneNumber", type="string", example="123456789")
     *                 )
     *)
     * ),
     * @SWG\Response(
     *         response=200,
     *         description="Returns new user id",
     *     @SWG\Schema(
     *     @SWG\Property(property="user_id", type="integer", example="1"),
     * )
     * )
     * @SWG\Response(
     *         response=401,
     *         description="Expired JWT Token | JWT Token not found | Invalid JWT Token",
     *     )
     */
    public function add(Request $request)
    {
        $result = $this->userService->add($request);
        if ($result instanceof User) {
            return $this->view(['user_id' => $result->getId()], Response::HTTP_OK);
        }
        return $this->view(['error' => $result], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Put("/user/{id}")
     * @param Request $request
     * @param User $user
     * @return View
     * @SWG\Put(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"User"},
     *     summary="Update existing user",
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
     *         description="User id"
     *     ),
     *     @SWG\Parameter(
     *       name="body",
     *       in="body",
     *       description="JSON user object",
     *       type="json",
     *       required=true,
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(property="name", type="string", example="Lorem"),
     *              @SWG\Property(property="surname", type="string", example="Ipsum"),
     *              @SWG\Property(property="email", type="string", example="lorem.ipsum@example.com")
     *                 )
     *)
     * ),
     * @SWG\Response(
     *         response=200,
     *         description="Returns success status"
     * )
     * @SWG\Response(
     *         response=401,
     *         description="Expired JWT Token | JWT Token not found | Invalid JWT Token",
     *     )
     */
    public function update(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted('USER_EDIT', $user);
        $result = $this->userService->update($request, $user);
        if ($result) {
            return $this->view([], Response::HTTP_OK);
        }
        return $this->view(['error' => $result], Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Rest\Delete("/user/{id}")
     * @param User $user
     * @IsGranted("ROLE_ADMIN",message="Only administrator can remove user.")
     * @return bool|View
     * @SWG\Delete(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     summary="Delete existing user",
     *     tags={"User"},
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
     *         description="User id"
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
     *         description="User not found",
     *     )
     */
    public function remove(User $user)
    {
        $result = $this->userService->remove($user);
        if ($result) {
            return $this->view([], Response::HTTP_NO_CONTENT);
        }
        return false;
    }

    /**
     * @Rest\Get("/user/{id}")
     * @param User $user
     * @return View
     * @SWG\Get(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     summary="Get existing user details",
     *     tags={"User"},
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
     *         description="User id"
     *     )
     * )
     * @SWG\Response(
     *         response=200,
     *         description="Returns user details",
     *          @Model(type=User::class)
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Expired JWT Token | JWT Token not found | Invalid JWT Token",
     *     ),
     * @SWG\Response(
     *         response=404,
     *         description="User not found",
     *     )
     */
    public function details(User $user)
    {
        $this->denyAccessUnlessGranted('USER_VIEW', $user);
        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/users")
     * @param Request $request
     * @IsGranted("ROLE_ADMIN",message="Only administrator can get users list.")
     * @return View
     * @SWG\Get(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     summary="Get users lists",
     *     tags={"User"},
     * @SWG\Parameter(
     *         name="Authorization",
     *         in="header",
     *         required=true,
     *         type="string",
     *         default="Bearer TOKEN",
     *         description="Authorization"
     *     ),
     * )
     * @SWG\Response(
     *         response=200,
     *         description="Returns users list",
     *     @SWG\Schema(
     *     type="array",
     *     @Model(type=User::class)
     * )
     *     ),
     * @SWG\Response(
     *         response=401,
     *         description="Expired JWT Token | JWT Token not found | Invalid JWT Token",
     *     )
     */
    public function list(Request $request)
    {
        $users = $this->userService->getUsers($request);
        return $this->view($users, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/user/{id}/tickets")
     * @param User $user
     * @param Request $request
     * @IsGranted("ROLE_ADMIN",message="Only administrator can get user tickets list.")
     * @return View
     * @SWG\Get(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     summary="Get user tickets",
     *     tags={"User"},
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
     *         in="header",
     *         required=true,
     *         type="string",
     *         description="User id"
     *     ),
     * )
     * @SWG\Response(
     *         response=200,
     *         description="Returns users ticket",
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
    public function userTickets(User $user, Request $request)
    {
        $tickets = $this->userService->getUserTickets($user, $request);
        return $this->view($tickets, Response::HTTP_OK);
    }
}