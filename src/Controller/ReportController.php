<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ReportPeriodType;
use App\Transaction\Enum\TransactionEnum;
use App\Transaction\Repository\TransactionRepository;
use App\Transaction\Service\TransactionService;
use App\Transaction\Trait\TransactionTrait;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ReportController extends AbstractController
{
	public function __construct(
		protected TransactionRepository $transactionRepository,
		protected TransactionService    $transactionService
	)
	{
	}
	
	use TransactionTrait;
	
	/**
	 * @param Request $request
	 * @return Response
	 */
	#[Route('/report', name: 'app_report', methods: ['GET', 'POST'])]
	public function index(#[CurrentUser] ?User $user, Request $request):
	Response
	{
		$form = $this->createForm(ReportPeriodType::class);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$formDate = $form->getData();
			
			$start = $formDate['dateFrom'];
			$end = $formDate['dateEnd'];
			
			$transactions = $this->transactionService->getTransactionsPerPeriod($user, $start, $end);
			$income = $this->transactionService->getSum($transactions, TransactionEnum::INCOME);
			$expense = $this->transactionService->getSum($transactions, TransactionEnum::EXPENSE);
			
			return $this->render('report/index.html.twig', [
				'income' => $income,
				'expense' => $expense,
				'form' => $form,
				'transactions' => $transactions
			]);
		}
		return $this->render('report/index.html.twig', [
			'income' => null,
			'expense' => null,
			'form' => $form,
			'transactions' => null
		]);
	}
	
}
