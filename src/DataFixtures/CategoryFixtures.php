<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class CategoryFixtures extends Fixture
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->client->request('GET', 'https://fakestoreapi.com/products/categories');

        $apiCategories = $data->toArray();

        foreach ($apiCategories as $data) {
            $category = new Category();
            $category->setName($data);
            $category->setCreatedAt(new \DateTime());

            $manager->persist($category);
        }

        $manager->flush();
    }
}