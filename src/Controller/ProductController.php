<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\Product\EditProductStock;
use App\Form\Product\EditProductType;
use App\Form\Product\ProductType;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/product')]
class ProductController extends AbstractController
{
    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    #[Route('/', name: 'product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $this->productService->getAllProducts();

        return $this->render('product/list.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/new', name: 'product_new', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form = $form->getData();
            $form->setCreatedAt(new \DateTime());
            $form->setStock(0);
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->persist($product);
                $entityManager->flush();
                $this->addFlash('success', 'Se añadió el producto ' . $form->getName() . ' correctamente');
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'No se ha podido crear el producto');
            }

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/list.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'product_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(EditProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Se modifico el producto correctamente');
            } catch (Exception $exception) {
                $this->addFlash('danger', 'No se ha podido editar el producto');
            }

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/stock', name: 'product_stock', methods: ['GET', 'POST'])]
    public function modifyStock(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        // Obtenemos la entidad en el momento de hacer la petición para saber la cantidad que tiene antes de modificarla
        $previousProductData = $entityManager->getUnitOfWork()->getOriginalEntityData($product);

        $form = $this->createForm(EditProductStock::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Desarrollamos la parte lógica en el Servicio enviando la entidad antigua y los valores a modificar que nos llegan en la petición
            // El servicio nos retornara FALSE en caso de que se esté solicitando eliminar más stock del actual a la hora de hacer la modificación
            $stockQty = $this->productService->checkIfPossibleUpdateStock($previousProductData, $product);

            if(!$stockQty) {
                $this->addFlash('danger', 'La cantidad total de stock para el producto no puede ser negativa');
            } else {
                $form = $form->getData();
                $form->setStock($stockQty);
                try {
                    $this->getDoctrine()->getManager()->flush();
                    $this->addFlash('success', 'Se modifico el stock del producto correctamente');
                } catch (Exception $exception) {
                    $this->addFlash('danger', 'No se ha podido editar el stock del producto');
                }
            }

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('product/stock.html.twig', [
            'product' => $product,
            'form' => $form
        ]);
    }
}
