<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class FAQController extends AbstractController
{
    #[IsGranted("IS_AUTHENTICATED_FULLY")]
    #[Route('/help', name: 'app_faq_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('faq/index.html.twig');
    }

    #[Route('/faq', name: 'app_faq_main', methods: ['GET'])]
    public function main(): Response
    {
        return $this->render('faq/main.html.twig');
    }

}