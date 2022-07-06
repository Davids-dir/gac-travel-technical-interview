<?php

namespace App\Service;

use App\Repository\ProductRepository;

class ProductService
{
    public function __construct
    (
        ProductRepository $productRepository
    )
    {
        $this->productRepository = $productRepository;
    }

    public function getAllProducts()
    {
        return $this->productRepository->findAll();
    }
}