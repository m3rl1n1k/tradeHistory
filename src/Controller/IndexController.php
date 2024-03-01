<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\TransactionRepository;
use App\Service\ChartService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class IndexController extends AbstractController
{
	public function __construct()
	{
	}
	
	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	#[Route('/home', name: 'app_home')]
	public function home(#[CurrentUser] ?User $user, TransactionRepository $transactionRepository, ChartService $chartService):
	Response
	{
		$chart = $chartService->dashboardChart($user, 'Expense');
		return $this->render('index/index.html.twig', [
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
