<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // Add user
        $user = new User();
        $user->setName("User");
        $user->setSurname("Ipsum");
        $user->setEmail("user@example.com");
        $encodedPassword = $this->encoder->encodePassword($user, "user123");
        $user->setPassword($encodedPassword);
        $user->setPhoneNumber("987654321");

        $manager->persist($user);

        // Add administrator
        $admin = new User();
        $admin->setName("Admin");
        $admin->setSurname("Ipsum");
        $admin->setEmail("admin@example.com");
        $encodedPassword = $this->encoder->encodePassword($admin, "admin123");
        $admin->setPassword($encodedPassword);
        $admin->setRoles(array('ROLE_ADMIN'));
        $admin->setPhoneNumber("123456789");

        $manager->persist($admin);

        $manager->flush();
    }
}
