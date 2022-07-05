<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\Category\CategoryType;
use App\Form\Category\EditCategoryType;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/category')]
class CategoryController extends AbstractController
{
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    #[Route('/', name: 'category_list', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $this->categoryService->getAllCategories();

        return $this->render('category/list.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/new', name: 'new_category', methods: ['GET','POST'])]
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form = $form->getData();
            $form->setCreatedAt(new \DateTime());
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->persist($category);
                $entityManager->flush();
                $this->addFlash('success', 'La categoría se ha creado correctamente');
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Se ha producido un error al realizar la operación');
                return  $this->render('category/new.html.twig');
            }

            return $this->redirectToRoute('category_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'category_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(EditCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Se ha editado la categoría correctamente');
            } catch (\Exception $exception) {
                $this->addFlash('danger', 'Error al editar la categoría');
            }

            return $this->redirectToRoute('category_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('category_list', [], Response::HTTP_SEE_OTHER);
    }
}
