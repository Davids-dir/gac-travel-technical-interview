<?php

namespace App\Service;

use App\Entity\Product;
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

    public function getStockNumberDiff(array $previousProductData, Product $product)
    {
        /** @var $checkStockPositiveOrNegative
         * return 1 for positive, 0 is value is data given is zero, -1 if negative
         */
        $checkStockPositiveOrNegative = gmp_sign($product->getStock());


        if ($checkStockPositiveOrNegative === 1) { // Value from request is positive
            $updateStockValue = $previousProductData['stock'] + $product->getStock();
        } elseif ($checkStockPositiveOrNegative === 0) { // Value from request is zero
            $updateStockValue = $previousProductData['stock'];
        } else { // Value from request is negative
            if (($previousProductData['stock'] - abs($product->getStock())) < 0) {  // Value after minus operation is negative, can't be negative
                return false;
            } else {
                $updateStockValue = $previousProductData['stock'] - abs($product->getStock());
            }
        }

        return $updateStockValue;
    }
}