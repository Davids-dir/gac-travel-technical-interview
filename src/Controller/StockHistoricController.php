<?php

namespace App\Controller;

use App\Entity\StockHistoric;
use App\Form\StockHistoricType;
use App\Repository\StockHistoricRepository;
use App\Service\ProductService;
use App\Service\StockHistoricService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/historic')]
class StockHistoricController extends AbstractController
{
    public function __construct(StockHistoricService $stockHistoricService, ProductService $productService)
    {
        $this->stockHistoricService = $stockHistoricService;
        $this->productService = $productService;
    }

    #[Route('/', name: 'stock_historic_index', methods: ['GET'])]
    public function index(StockHistoricRepository $stockHistoricRepository): Response
    {


        return $this->render('stock_historic/index.html.twig', [
            'stock_historics' => $stockHistoricRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'stock_historic_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $stockHistoric = new StockHistoric();
        $form = $this->createForm(StockHistoricType::class, $stockHistoric);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($stockHistoric);
            $entityManager->flush();

            return $this->redirectToRoute('stock_historic_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock_historic/new.html.twig', [
            'stock_historic' => $stockHistoric,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'stock_historic_show', methods: ['GET', 'POST'])]
    public function show(Request $request/*, StockHistoric $stockHistoric*/): Response
    {
        $stockHistorics = $this->stockHistoricService->getHistoricByProductId($request->attributes->get('id'));
        $product = $this->productService->getProductById($request->attributes->get('id'));

        $differences = $this->stockHistoricService->getStockDifferencesEachRegister($stockHistorics);

        return $this->render('stock_historic/show.html.twig', [
            'stock_historics' => $stockHistorics,
            'product_name' => $product->getName(),
            'diff' => $differences
        ]);
    }

    #[Route('/{id}/edit', name: 'stock_historic_edit', methods: ['GET','POST'])]
    public function edit(Request $request, StockHistoric $stockHistoric): Response
    {
        $form = $this->createForm(StockHistoricType::class, $stockHistoric);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('stock_historic_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock_historic/edit.html.twig', [
            'stock_historic' => $stockHistoric,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'stock_historic_delete', methods: ['POST'])]
    public function delete(Request $request, StockHistoric $stockHistoric): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stockHistoric->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($stockHistoric);
            $entityManager->flush();
        }

        return $this->redirectToRoute('stock_historic_index', [], Response::HTTP_SEE_OTHER);
    }
}
