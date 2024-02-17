<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ChartService;
use App\Transaction\Repository\TransactionRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class IndexController extends AbstractController
{
	public function __construct(
		protected TransactionRepository $transactionRepository,
	)
	{
	}
	
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	#[Route('/home', name: 'app_home')]
	public function home(#[CurrentUser] ?User $user, TransactionRepository $transactionRepository, ChartService $chartService):
	Response
	{
		$transactions = $transactionRepository->getUserTransactions($user->getUserId());
		$chart = $chartService->dashboardChart($transactions);
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
