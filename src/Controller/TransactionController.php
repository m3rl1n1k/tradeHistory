<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\TransactionType;
use App\Transaction\Entity\Transaction;
use App\Transaction\Enum\TransactionEnum;
use App\Transaction\Repository\TransactionRepository;
use App\Transaction\Service\TransactionService;
use App\Transaction\Trait\TransactionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/transaction')]
class TransactionController extends AbstractController
{
	public function __construct(
		protected TransactionRepository $transactionRepository,
		protected TransactionService    $transactionService
	)
	{
	}
	
	use TransactionTrait;
	
	#[Route('/', name: 'app_transaction_index', methods: ['GET'])]
	public function index(#[CurrentUser] ?User $user, Request $request): Response
	{
		$query = $this->transactionService->getTransactionListByUser($user);
		return $this->render('transaction/index.html.twig', [
			'pagerfanta' => $this->paginate($query, $request),
		]);
	}
	
	#[Route('/new', name: 'app_transaction_new', methods: ['GET', 'POST'])]
	public function new(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager): Response
	{
		$transaction = new Transaction();
		$form = $this->createForm(TransactionType::class, $transaction);
		
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$formData = $form->getData();
			$formData->setUserId($user);
			
			$this->transactionService->setUserAmount($user, $transaction);
			
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
	public function show(#[CurrentUser] ?User $user, Transaction $transaction): Response
	{
		$this->accessDenied($transaction, $user);
		return $this->render('transaction/show.html.twig', [
			'transaction' => $transaction,
		]);
	}
	
	#[Route('/{id}/edit', name: 'app_transaction_edit', methods: ['GET', 'POST'])]
	public function edit(#[CurrentUser] ?User $user, Request $request, Transaction $transaction, EntityManagerInterface $entityManager): Response
	{
		$this->accessDenied($transaction, $user);
		
		$oldAmount = $transaction->getAmount();
		$form = $this->createForm(TransactionType::class, $transaction);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			
			$this->transactionService->calculateAmount($user, $transaction, $oldAmount);
			
			$entityManager->flush();
			
			return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
		}
		return $this->render('transaction/edit.html.twig', [
			'transaction' => $transaction,
			'form' => $form,
		]);
	}
	
	#[Route('/{id}', name: 'app_transaction_delete', methods: ['POST'])]
	public function delete(#[CurrentUser] ?User   $user, Request $request, Transaction $transaction,
						   EntityManagerInterface $entityManager): Response
	{
		$this->accessDenied($transaction, $user);
		
		if ($this->isCsrfTokenValid('delete' . $transaction->getId(), $request->request->get('_token'))) {
			$this->transactionService->removeTransaction($user, $transaction);
			$entityManager->remove($transaction);
			$entityManager->flush();
		}
		
		return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
	}
}
