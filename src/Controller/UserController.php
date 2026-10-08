<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/{id}/add/editor', name: 'app_user_add_editor_role')]
    public function editorRoleAdd(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('user_role_' . $user->getId(), (string) $request->query->get('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide');
            return $this->redirectToRoute('app_user');
        }

        $roles = $user->getRoles();
        $roles[] = 'ROLE_EDITOR';
        $user->setRoles(array_values(array_unique($roles)));
        $entityManager->flush();

        $this->addFlash('success', 'Le rôle éditeur a été ajouté à l\'utilisateur');
        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/remove/editor', name: 'app_user_remove_editor_role')]
    public function editorRoleRemove(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('user_role_' . $user->getId(), (string) $request->query->get('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide');
            return $this->redirectToRoute('app_user');
        }

        // On retire uniquement le rôle éditeur, les autres rôles sont conservés
        $roles = array_diff($user->getRoles(), ['ROLE_EDITOR']);
        $user->setRoles(array_values($roles));
        $entityManager->flush();

        $this->addFlash('danger', 'Le rôle éditeur a été retiré à l\'utilisateur');
        return $this->redirectToRoute('app_user');
    }

    #[Route('/admin/user/{id}/delete', name: 'app_user_delete')]
    public function userDelete(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        if (!$this->isCsrfTokenValid('user_delete_' . $user->getId(), (string) $request->query->get('_token'))) {
            $this->addFlash('danger', 'Jeton de sécurité invalide');
            return $this->redirectToRoute('app_user');
        }

        if ($user === $this->getUser()) {
            $this->addFlash('danger', 'Vous ne pouvez pas supprimer votre propre compte');
            return $this->redirectToRoute('app_user');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('danger', 'L\'utilisateur a été supprimé');
        return $this->redirectToRoute('app_user');
    }
}
