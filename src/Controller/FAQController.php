<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FAQController extends AbstractController
{
    #[Route('/faq', name: 'app_faq_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('faq/index.html.twig');
    }
}