<?php

namespace App\Controller;

use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
	public function __construct(
        protected TransactionRepository $transactionRepository
	)
	{
	}

	#[Route('/', name: 'app_index')]
	public function index(): Response
	{
		return $this->render('index/index.html.twig', [
            'transactions' => $this->transactionRepository->findAll(),
        ]);
	}
}
