<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserService
{
    private UserRepository $userRepository;
    private ValidatorInterface $validator;
    private PaginatorInterface $paginator;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(
        UserRepository $userRepository,
        ValidatorInterface $validator,
        PaginatorInterface $paginator,
        UserPasswordEncoderInterface $encoder)
    {
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->paginator = $paginator;
        $this->encoder = $encoder;
    }

    public function add(Request $request)
    {
        $userData = json_decode($request->getContent(), true);
        $name = $userData['name'];
        $surname = $userData['surname'];
        $email = $userData['email'];
        $password = $userData['password'];

        $user = new User();
        $user->setName($name);
        $user->setSurname($surname);
        $user->setEmail($email);
        $encodedPassword = $this->encoder->encodePassword($user, $password);
        $user->setPassword($encodedPassword);
        $user->setRoles(array('ROLE_ADMIN'));

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            throw new BadRequestHttpException($errors);
        }

        $this->userRepository->save($user);
        return $user;
    }

    public function update(Request $request, User $user): User
    {
    }

    public function remove(User $user): bool
    {
        $this->userRepository->remove($user);
        return true;
    }

    public function getUsers(Request $request): iterable
    {
    }
}