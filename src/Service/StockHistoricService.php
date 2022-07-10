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

        // Introduzco en el array el stock de cada registro
        foreach ($stockHistorics as $stockHistoric) {
            array_push($stockQtyDiff, $stockHistoric->getStock());
        }

        // Me traigo stock del primer registro del producto para iniciar el cálculo
        // Si el producto nunca ha sufrido cambios de stock declaro un array vacío
        $stockHistorics
            ? $differences[] = $stockHistorics[0]->getStock()
            : $differences = [];

        // Comparo la diferencia de stock entre el primer elemento del array con el siguiente y asi de forma sucesiva
        // Inserto los valores en un array para devolver los datos comparados y poder mostrarlos en el template
        for ($i = 1, $n = count($stockQtyDiff); $i < $n; $i ++) {
            $differences[] = $stockQtyDiff[$i] - $stockQtyDiff[$i - 1];
        }

        return $differences;
    }
}