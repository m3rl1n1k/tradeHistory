<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserSettingType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class UserSettingController extends AbstractController
{
    #[Route('/setting', name: 'app_user_setting_index', methods: ['GET', 'POST'])]
    public function settings(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $userSetting */
        $userSetting = $this->getUser()->getSetting();
        $form = $this->createForm(UserSettingType::class, $userSetting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userSetting);
            $entityManager->flush();
            $this->addFlash('success', 'Settings is saved');
            return $this->redirectToRoute('app_user_setting_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('user_setting/index.html.twig', [
            'form' => $form,
        ]);
    }
}