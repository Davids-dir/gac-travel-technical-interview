<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Category;
use App\Service\CategoryService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ProductFixtures extends Fixture
{
    private $client;
    private $categoryService;

    public function __construct(HttpClientInterface $client, CategoryService $categoryService)
    {
        $this->client = $client;
        $this->categoryService = $categoryService;
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->client->request('GET', 'https://fakestoreapi.com/products?limit=12');

        $apiProducts = $data->toArray();

        foreach ($apiProducts as $data) {

            /**
             * @var $category
             * @return Category
             */
            $category = $this->categoryService->getCategoryByName($data['category']);

            $product = new Product();
            $product->setName($data['title']);
            $product->setCategory($category);
            $product->setCreatedAt(new \DateTime());
            $product->setStock(0);

            $manager->persist($product);
        }

        $manager->flush();
    }
}