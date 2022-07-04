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
    private $tokenId;

    public function __construct
    (
        UserService $userService,
        string $tokenId
    )
    {
        $this->userService = $userService;
        $this->tokenId = $tokenId;
    }

    #[Route('/list', name: 'list_users', methods: ['GET'])]
    public function list(): Response
    {
        if (in_array($this->roleAdmin, $this->getUser()->getRoles())) {
            $users = $this->userService->getAllUsers();

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

    #[Route('/{id}/edit', name: 'edit_user', methods: ['GET','POST'])]
    public function edit(Request $request, User $user, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form = $form->getData();

            // Hash current password
            $form->setPassword($passwordHasher->hashPassword($form, $form->getPassword()));
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

    #[Route('/{csrf}/{id}', name: 'delete_user', methods: ['GET', 'POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->getUser()->getId() === $user->getId())
        {
            $this->addFlash('danger', 'No puedes eliminar el usuario con el que estas conectado');

            return $this->redirectToRoute('list_users', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid($this->tokenId, $request->get('csrf'))) {
            $entityManager = $this->getDoctrine()->getManager();
            try {
                $entityManager->remove($user);
                $entityManager->flush();
                $this->addFlash('success', 'El usuario ha sido eliminado');
            } catch (\Exception $e) {
                $this->addFlash('danger', 'No se ha podido eliminar el usuario');
            }
        }

        return $this->redirectToRoute('list_users', [], Response::HTTP_SEE_OTHER);
    }
}
