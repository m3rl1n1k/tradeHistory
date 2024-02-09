<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ReportPeriodType;
use App\Transaction\Repository\TransactionRepository;
use App\Transaction\Trait\TransactionTrait;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

class ReportController extends AbstractController
{
	private UserInterface|User $user;
	
	public function __construct(
		protected TransactionRepository $transactionRepository,
		protected Security              $security,
	)
	{
	}
	
	use TransactionTrait;
	
	/**
	 * @param Request $request
	 * @param TransactionRepository $transactionRepository
	 * @return Response
	 * @throws NonUniqueResultException
	 */
	#[Route('/report', name: 'app_report', methods: ['GET', 'POST'])]
	public function index(Request $request, TransactionRepository $transactionRepository): Response
	{
		$userId = $this->security->getUser()->getId();
		
		$form = $this->createForm(ReportPeriodType::class);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$formDate = $form->getData();
			
			$start = $formDate['dateFrom'];
			$end = $formDate['dateEnd'];
			$data = $this->paginate($transactionRepository->getTransactionsPerPeriod($start, $end), $request, inf: true);
			return $this->redirectToRoute('app_report_show', [], Response::HTTP_SEE_OTHER);
		}
		$income = $this->transactionRepository->getSumIncome($userId);
		$expense = $this->transactionRepository->getSumExpense($userId);
		return $this->render('report/index.html.twig', [
			'income' => $income,
			'expense' => $expense,
			'form' => $form,
		]);
	}
	
	#[Route('/records', name: 'app_report_show', methods: ['POST'])]
	public function show($data): Response
	{
		return $this->render('report/records.html.twig', [
			'pagerfanta' => $data
		]);
	}
	
}
