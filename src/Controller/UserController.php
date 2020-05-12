<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends AbstractFOSRestController
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Rest\Post("/user")
     * @param Request $request
     * @return View
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
     */
    public function update(Request $request, User $user)
    {
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
     */
    public function details(User $user)
    {
        return $this->view($user, Response::HTTP_OK);
    }

    /**
     * @Rest\Get("/users")
     * @param Request $request
     * @IsGranted("ROLE_ADMIN",message="Only administrator can get users list.")
     * @return View
     */
    public function list(Request $request)
    {
        $users = $this->userService->getUsers($request);
        return $this->view($users, Response::HTTP_OK);
    }
}