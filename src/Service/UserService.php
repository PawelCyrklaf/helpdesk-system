<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\TicketRepository;
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
    private ErrorService $errorService;
    private TicketRepository $ticketRepository;

    public function __construct(
        UserRepository $userRepository,
        ValidatorInterface $validator,
        PaginatorInterface $paginator,
        UserPasswordEncoderInterface $encoder,
        ErrorService $errorService,
        TicketRepository $ticketRepository)
    {
        $this->userRepository = $userRepository;
        $this->validator = $validator;
        $this->paginator = $paginator;
        $this->encoder = $encoder;
        $this->errorService = $errorService;
        $this->ticketRepository = $ticketRepository;
    }

    public function add(Request $request)
    {
        $userData = json_decode($request->getContent(), true);
        $name = $userData['name'];
        $surname = $userData['surname'];
        $email = $userData['email'];
        $password = $userData['password'];
        $phoneNumber = $userData['phoneNumber'];


        $user = new User();
        $user->setName($name);
        $user->setSurname($surname);
        $user->setEmail($email);
        $encodedPassword = $this->encoder->encodePassword($user, $password);
        $user->setPassword($encodedPassword);
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setPhoneNumber($phoneNumber);
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return $this->errorService->formatError($errors);
        }

        $this->userRepository->save($user);
        return $user;
    }

    public function update(Request $request, User $user): User
    {
        $userData = json_decode($request->getContent(), true);
        $name = $userData['name'];
        $surname = $userData['surname'];
        $email = $userData['email'];

        if (!$name) {
            throw new BadRequestHttpException('Name cannot be empty!');
        }
        $user->setName($name);
        if (!$surname) {
            throw new BadRequestHttpException('Surname cannot be empty!');
        }
        $user->setSurname($surname);
        if (!$email) {
            throw new BadRequestHttpException('Email cannot be empty!');
        }
        $user->setEmail($email);

        $this->userRepository->update();
        return $user;
    }

    public function remove(User $user): bool
    {
        $this->userRepository->remove($user);
        return true;
    }

    public function getUsers(Request $request): iterable
    {
        $users = $this->userRepository->findAll();
        return $this->paginator->paginate($users, $request->query->getInt('page', 1), $request->query->getInt('limit', 10))->getItems();
    }

    public function getUserTickets(User $user, Request $request): iterable
    {
        $tickets = $this->ticketRepository->findBy(array('author' => $user));
        return $this->paginator->paginate($tickets, $request->query->getInt('page', 1), $request->query->getInt('limit', 10))->getItems();
    }
}