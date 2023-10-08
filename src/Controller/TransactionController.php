<?php

namespace App\Controller;

use App\Entity\Transaction;
use App\Form\TransactionType;
use App\Repository\TransactionRepository;
use App\Services\TransactionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction')]
class TransactionController extends AbstractController
{
	public function __construct(
		protected TransactionService $transactionService,
		protected Security           $security
	)
	{
	}

	#[Route('/', name: 'app_transaction_index', methods: ['GET'])]
	public function index(TransactionRepository $transactionRepository): Response
	{
		return $this->render('transaction/index.html.twig', [
			'transactions' => $transactionRepository->findAll(),
		]);
	}

	#[Route('/new', name: 'app_transaction_new', methods: ['GET', 'POST'])]
	public function new(Request $request, EntityManagerInterface $entityManager): Response
	{
		$user = $this->security->getUser();
		$transaction = new Transaction();
		$form = $this->createForm(TransactionType::class, $transaction);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$this->transactionService->testCalculating($user, $transaction);
			$transaction->setUserId($user);
			$entityManager->persist($transaction);
			$entityManager->flush();
			return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('transaction/new.html.twig', [
			'transaction' => $transaction,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'app_transaction_show', methods: ['GET'])]
	public function show(Transaction $transaction): Response
	{
		return $this->render('transaction/show.html.twig', [
			'transaction' => $transaction,
		]);
	}

	#[Route('/{id}/edit', name: 'app_transaction_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
	{
		$oldAmount = $this->transactionService->getOldAmount($transaction);
		$form = $this->createForm(TransactionType::class, $transaction);
		$form->handleRequest($request);

		$user = $this->security->getUser();
		$newAmount = $form->get('amount')->getData();

		if ($form->isSubmitted() && $form->isValid()) {

			$this->transactionService->testCalcEdit($user, $transaction, $oldAmount, $newAmount);
			$entityManager->flush();

			return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('transaction/edit.html.twig', [
			'transaction' => $transaction,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'app_transaction_delete', methods: ['POST'])]
	public function delete(Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
	{
		if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->request->get('_token'))) {
			$entityManager->remove($transaction);
			$entityManager->flush();
		}

		return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
	}
}
