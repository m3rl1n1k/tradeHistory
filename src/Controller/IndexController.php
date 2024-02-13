<?php

namespace App\Controller;

use App\Entity\User;
use App\Transaction\Repository\TransactionRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class IndexController extends AbstractController
{
	private UserInterface|User|null $user;
	
	public function __construct(
		protected TransactionRepository $transactionRepository,
		protected Security              $security,
	)
	{
		$this->user = $this->security->getUser();
	}
	
	/**
	 * @throws NonUniqueResultException
	 */
	#[IsGranted('IS_AUTHENTICATED_FULLY')]
	#[Route('/home', name: 'app_home')]
	public function home(TransactionRepository $transactionRepository, ChartBuilderInterface $chartBuilder): Response
	{
		$chart = $chartBuilder->createChart(Chart::TYPE_LINE);
		
		$chart->setData([
			'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
			'datasets' => [
				[
					'label' => 'My First dataset',
					'backgroundColor' => 'rgb(255, 99, 132)',
					'borderColor' => 'rgb(255, 99, 132)',
					'data' => [0, 10, 5, 2, 20, 30, 45],
				],
			],
		]);
		
		$chart->setOptions([
			'scales' => [
				'y' => [
					'suggestedMin' => 0,
					'suggestedMax' => 100,
				],
			],
		]);
		$userId = $this->user->getId();
		return $this->render('index/index.html.twig', [
			'chart' => $chart,
			'last10transaction' => $transactionRepository->findBy(
				[
					'user' => $userId
				],
				orderBy: [
					'id' => 'DESC'
				],
				limit: 10
			),
			'income' => $transactionRepository->getSumIncome($userId),
			'expense' => $transactionRepository->getSumExpense($userId)
		]);
	}
	
	#[Route('/', name: 'app_index')]
	public function index(): Response
	{
		return !$this->user ? $this->redirectToRoute('app_login') : $this->redirectToRoute('app_home');
	}
}
