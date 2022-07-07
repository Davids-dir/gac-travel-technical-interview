<?php

namespace App\EventListener;

use App\Entity\Product;
use App\Entity\StockHistoric;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class ProductEditStockListener
{
    public function __construct(TokenStorageInterface $tokenStorage, EntityManagerInterface $entityManagerInterface)
    {
        $this->tokenStorage = $tokenStorage;
        $this->entityManagerInterface = $entityManagerInterface;
    }

    public function postUpdate(Product $product, LifecycleEventArgs $event): void
    {
        $entity = $event->getObject();

        if (!$entity instanceof Product) {
            return;
        }

        $user = $this->tokenStorage->getToken()->getUser();
        $entityManager = $event->getObjectManager();

        // Esto nos retornará un array de 2 elementos
        // En la posición 0 encontraremos el valor anterior, en la posición 1 encontraremos el valor actual del stock del producto
        $dataBeforeUpdateEntity = $this->entityManagerInterface->getUnitOfWork()->getEntityChangeSet($product);

        // Código que insertara en la tabla de StockHistoric el valor positivo o negativo
        // Esto solo se ejecuta cuando se detecta un cambio que afecte a la propiedad STOCK del producto
        // No se ejecutara si modificamos otras propiedades tales como el NOMBRE o la CATEGORÍA del producto
        if (!empty($dataBeforeUpdateEntity)) {
            // ¿La cantidad anterior es menor a la que nos llega?
            // RECORDAMOS QUES ESTO SOLO VA A SUCEDER SI SUPERA LA LÓGICA QUE APLICAMOS AL STOCK EN EL CONTROLADOR
            $dataBeforeUpdateEntity['stock'][0] < $entity->getStock()
                ?  $stockValue = abs($dataBeforeUpdateEntity['stock'][0] - $entity->getStock()) // Entonces se está añadiendo stock al producto, conseguimos el valor absoluto o la diferencia entre la cantidad antigua y la nueva
                :   $stockValue = '-' . abs($dataBeforeUpdateEntity['stock'][0] - $entity->getStock()); // En este caso es que se está restando, obtenemos la diferencia y añadimos el negativo delante para devolver la cantidad restada
        }

        // Este código se emplea en caso de que tan solo queramos añadir la cantidad de stock total en el registro de StockHistoric
        // $stockValue = $product->getStock();

        $stockHistoric = new StockHistoric();
        $stockHistoric->setStock(intval($stockValue));
        $stockHistoric->setCreatedAt(new \DateTime());
        $stockHistoric->setUser($user);
        $stockHistoric->setProduct($product);

        $entityManager->persist($stockHistoric);
        $entityManager->flush();
    }
}