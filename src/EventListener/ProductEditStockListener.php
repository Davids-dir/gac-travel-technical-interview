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
        $stockValue = $product->getStock();
        $entityManager = $event->getObjectManager();

        $stockHistoric = new StockHistoric();
        $stockHistoric->setStock(intval($stockValue));
        $stockHistoric->setCreatedAt(new \DateTime());
        $stockHistoric->setUser($user);
        $stockHistoric->setProduct($product);

        $entityManager->persist($stockHistoric);
        $entityManager->flush();
    }
}