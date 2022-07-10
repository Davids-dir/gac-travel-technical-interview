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

    public function getProductById(int $id)
    {
        return $this->productRepository->findOneBy(['id' => $id]);
    }

    public function getAllProducts()
    {
        return $this->productRepository->findAll();
    }

    public function checkIfPossibleUpdateStock(array $previousProductData, Product $product)
    {
        /** @var $checkStockPositiveOrNegative
         * return 1 for positive, 0 is value is data given is zero, -1 if negative
         */
        $checkStockPositiveOrNegative = gmp_sign($product->getStock());


        if ($checkStockPositiveOrNegative === 1) { // La cantidad a modificar que nos llega en la petición tiene un valor positivo
            $updateStockValue = $previousProductData['stock'] + $product->getStock();
        } elseif ($checkStockPositiveOrNegative === 0) { // La cantidad a modificar que nos llega en la petición es cero
            $updateStockValue = $previousProductData['stock'];
        } else { // La cantidad a modificar que nos llega en la petición tiene un valor negativo
            if (($previousProductData['stock'] - abs($product->getStock())) < 0) {  // Código para saber si tras la operación sobre la cantidad actual nos devuelve negativo, retornaremos FALSE. No es posible dejar el stock en negativo
                return false;
            } else { // Retornamos la cantidad en caso de poder realizar la operación de resta sobre el stock actual antes de la actualización de la entidad
                $updateStockValue = $previousProductData['stock'] - abs($product->getStock());
            }
        }

        return $updateStockValue;
    }
}