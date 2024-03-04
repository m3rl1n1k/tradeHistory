<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\TransactionRepository;
use App\Service\ChartService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
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
	
	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	#[Route('/home', name: 'app_home', methods: ['GET'])]
	public function home(#[CurrentUser] ?User $user, TransactionRepository $transactionRepository, Request $request):
	Response
	{
		$categoryList = $request->query->keys();
		$chart = $this->chartService->dashboardChart($user, ['categories' => $categoryList]);
		return $this->render('index/index.html.twig', [
			'categories_list' => $categoryList,
			'categories' => $this->categoryRepository->getAll($user),
			'chart' => $chart,
			'last10transaction' => $transactionRepository->getUserTransactions($user->getUserId(), ['id' => 'DESC'], 10)
		]);
	}
	
	#[Route('/', name: 'app_index')]
	public function index(#[CurrentUser] ?User $user): Response
	{
		return !$user ? $this->redirectToRoute('app_login') : $this->redirectToRoute('app_home');
	}
	
}
