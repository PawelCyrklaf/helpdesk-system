<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;

class UserService
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function add(Request $request)
    {
    }

    public function update(Request $request, User $user): User
    {
    }

    public function remove(User $user): bool
    {
        $this->userRepository->remove($user);
        return true;
    }

    public function list(Request $request): iterable
    {
    }
}