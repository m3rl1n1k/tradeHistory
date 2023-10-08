<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\DepositRepository;
use App\Repository\TransactionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
	public function __construct(
		protected CategoryRepository    $categoryRepository,
		protected TransactionRepository $transactionRepository,
		protected DepositRepository     $depositRepository
	)
	{
	}

	#[Route('/', name: 'app_index')]
	public function index(): Response
	{
		return $this->render('index/index.html.twig', [
			'categories' => $this->categoryRepository->findAll(),
			'transactions' => $this->transactionRepository->findAll(),
			'deposits' => $this->depositRepository->findAll(),
		]);
	}
}
