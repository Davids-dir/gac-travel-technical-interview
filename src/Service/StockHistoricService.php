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

    public function getStockDifferencesEachRegister(array $stockHistorics): array
    {
        $stockQtyDiff = [];
        foreach ($stockHistorics as $stockHistoric) {
            array_push($stockQtyDiff, $stockHistoric->getStock());
        }

        $stockHistorics
            ? $differences[] = $stockHistorics[0]->getStock()
            : $differences = [];

        for ($i = 1, $n = count($stockQtyDiff); $i < $n; $i ++) {
            $differences[] = $stockQtyDiff[$i] - $stockQtyDiff[$i - 1];
        }

        return $differences;
    }
}