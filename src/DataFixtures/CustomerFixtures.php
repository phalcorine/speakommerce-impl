<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRoleType;
use App\Utility\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CustomerFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setName('John Smith')
            ->setRoles([UserRoleType::ROLE_STORE_USER])
            ->setEmail('john.smith@email.com')
            ->setToken(TokenGenerator::generateUserToken())
            ->setPassword($this->passwordHasher->hashPassword($user1, 'john@123'));
        $manager->persist($user1);

        $user2 = new User();
        $user2->setName('Mary Daniels')
            ->setRoles([UserRoleType::ROLE_STORE_USER])
            ->setEmail('mary.daniels@email.com')
            ->setToken(TokenGenerator::generateUserToken())
            ->setPassword($this->passwordHasher->hashPassword($user2, 'mary@123'));
        $manager->persist($user2);

        $manager->flush();
    }
}
