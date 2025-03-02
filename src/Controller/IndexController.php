<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function index(): RedirectResponse
    {
        if ($this->getUser() === null) {
            $uri = $this->redirectToRoute('app_login');
        } else {
            $uri = $this->redirectToRoute('app_home');
        }
        return $uri;
    }


}