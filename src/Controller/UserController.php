<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;

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
     */
    public function add(Request $request)
    {
    }

    /**
     * @Rest\Put("/user/{id}")
     * @param User $user
     */
    public function update(User $user)
    {
    }

    /**
     * @Rest\Delete("/user/{id}")
     * @param User $user
     */
    public function remove(User $user)
    {
    }

    /**
     * @Rest\Get("/user/{id}")
     * @param User $user
     */
    public function details(User $user)
    {
    }

    /**
     * @Rest\Get("/users")
     * @param Request $request
     */
    public function list(Request $request)
    {
    }

}