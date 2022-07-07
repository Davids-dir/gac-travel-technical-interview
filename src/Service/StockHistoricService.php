<?php

namespace App\Service;

use App\Repository\StockHistoricRepository;

class StockHistoricService
{
    public function __construct(StockHistoricRepository $stockHistoricRepository)
    {
        $this->stockHistoricRepository = $stockHistoricRepository;
    }

    public function getHistoricByProductId($productId)
    {
        return $this->stockHistoricRepository->findBy(['product' => $productId]);
    }
}