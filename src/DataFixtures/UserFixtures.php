<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Util\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    use UserRole;
    private $client;
    private $passwordHasher;

    public function __construct(HttpClientInterface $client, UserPasswordHasherInterface $passwordHasher)
    {
        $this->client = $client;
        $this->hasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Limit to 10 entries from API
        $data = $this->client->request('GET', 'https://fakestoreapi.com/users?limit=10');

        $apiUsers = $data->toArray();

        foreach ($apiUsers as $data) {
            $user = new User();
            $user->setUsername($data['username']);
            $hashedPassword = $this->hasher->hashPassword($user, $data['username']);
            $user->setPassword($hashedPassword);
            $user->setActive(0);
            $user->setCreatedAt(new \DateTime());

            $manager->persist($user);
        }

        $testUser = new User();
        $testUser->setUsername('admin');
        $hashedPassword = $this->hasher->hashPassword(
            $testUser,
            'admin'
        );
        $testUser->setPassword($hashedPassword);
        $testUser->setRoles([$this->roleAdmin]);
        $testUser->setActive(1);
        $testUser->setCreatedAt(new \DateTime());

        $manager->persist($testUser);

        $manager->flush();
    }
}