<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use App\Service\ChartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class IndexController extends AbstractController
{
	public function __construct(
		protected ChartService       $chartService,
		protected CategoryRepository $categoryRepository
	)
	{
	}
	
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	#[Route('/home', name: 'app_home', methods: ['GET'])]
	public function home(TransactionRepository $transactionRepository, Request $request):
	Response
	{
		
		$categoryList = $request->query->keys();
//		$chart = $this->chartService->dashboardChart($user, ['categories' => $categoryList]);
		return $this->render('index/index.html.twig', [
			'categories_list' => $categoryList,
			'categories' => $this->categoryRepository->getAll(),
			'chart' => $chart ?? null,
			'last10transaction' => $transactionRepository->getUserTransactions(['date' => 'DESC'], 10)
		]);
	}

	#[Route('/', name: 'app_index')]
	public function index(): RedirectResponse
	{
		if (!$this->getUser()) {
			$uri = $this->redirectToRoute('app_login');
		}else{
			$uri = $this->redirectToRoute('app_home');
		}
		return $uri;
	}
	
}
