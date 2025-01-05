<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\SettingUserType;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Trait\AccessTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/user')]
class UserController extends AbstractController
{
    use AccessTrait;

    public function __construct(
        protected UserRepository $userRepository)
    {
    }

    #[Route('/{id}', name: 'app_user_index', methods: ['GET', 'POST'])]
    public function index(int $id, Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', ['id' => $id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/index.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/settings/{id}', name: 'app_user_settings', methods: ['GET', 'POST'])]
    public function editSettings(int $id, Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $settings = $user->getSetting();
        if (is_null($settings)) {
            $user->setSetting(null);
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Refresh page');
        } else {
            $form = $this->createForm(SettingUserType::class, $settings);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setSetting($form->getData());
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Settings is saved');
                return $this->redirectToRoute('app_user_settings', ['id' => $id], Response::HTTP_SEE_OTHER);
            }

            return $this->render('user/setting_user.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        return $this->render('user/setting_user.html.twig', [
            'form' => null,
        ]);
    }

    #[Route('/remove/{id}', name: 'app_remove_account', methods: ['GET', 'POST'])]
    public function removeAccount(int $id): void
    {

    }


}
