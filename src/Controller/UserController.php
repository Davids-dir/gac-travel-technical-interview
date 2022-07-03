<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\EditUserType;
use App\Form\User\NewUserType;
use App\Service\UserService;
use App\Util\UserRole;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user')]
class UserController extends AbstractController
{
    use UserRole;

    public function __construct
    (
        UserService $userService
    )
    {
        $this->userService = $userService;
    }

    #[Route('/list', name: 'list_users', methods: ['GET'])]
    public function list(): Response
    {
        if (in_array($this->roleAdmin, $this->getUser()->getRoles())) {
            $users[] = $this->userService->getAllUsers();

            return $this->render('user/list.html.twig', [
                'users' => $users,
            ]);
        }

        if (in_array($this->roleUser, $this->getUser()->getRoles())) {
            $users[] = $this->getUser();

            return $this->render('user/list.html.twig', [
                'users' => $users,
            ]);
        }
    }

    #[Route('/sign-up', name: 'sign_up', methods: ['GET','POST'])]
    public function new(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(NewUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form = $form->getData();

            // Hash current password
            $form->setPassword($passwordHasher->hashPassword($form, $form->getPassword()));
            $form->setCreatedAt(new \DateTime());
            $form->setActive(true);
            $entityManager = $this->getDoctrine()->getManager();

            try {
                $entityManager->persist($user);
                $entityManager->flush();
            } catch (\Exception $e) {
                return $this->render('error.html.twig', ['message' => "Error al crear el usuario"]);
            }

            return $this->render('success.html.twig');
        }

        return $this->renderForm('user/sign_up.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit_user', methods: ['GET','POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', "Se ha editado al usuario correctamente");
            } catch (\Exception $e) {
                $this->addFlash('danger', "Error al editar los campos de usuario");
            }

            return $this->redirectToRoute('list_users', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'delete_user', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index', [], Response::HTTP_SEE_OTHER);
    }
}
