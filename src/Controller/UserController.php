<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/{id}', name: 'app_user_index', methods: ['GET', 'POST'])]
    public function index(int $id, Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
//        $removeUser = $this->createFormBuilder();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Profile is saved');
            return $this->redirectToRoute('app_user_index', ['id' => $id], Response::HTTP_SEE_OTHER);
        }
        return $this->render('user/index.html.twig', [
            'form_user' => $form,
//            'remove_user' => $removeUser->getForm(),
        ]);
    }

    #[Route('/remove/{id}', name: 'app_remove_account', methods: ['POST'])]
    public function removeAccount(User $user, EntityManagerInterface $entityManager, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->beginTransaction();
                $entityManager->remove($user);
                $entityManager->flush();
                $entityManager->commit();
                return $this->redirectToRoute('app_login');

            } catch (Exception $e) {
                $entityManager->rollback();
            }
        }
        return $this->redirectToRoute('app_login');
    }
}