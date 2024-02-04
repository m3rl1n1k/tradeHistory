<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\TransactionType;
use App\Transaction\Entity\Transaction;
use App\Transaction\Repository\TransactionRepository;
use App\Transaction\Sevice\TransactionService;
use App\Transaction\Trait\TransactionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/transaction')]
class TransactionController extends AbstractController
{
	private UserInterface|User $user;
	
	public function __construct(
		protected TransactionRepository $transactionRepository,
		protected TransactionService    $transactionService
	)
	{
	}
	
	use TransactionTrait;
	
	#[Route('/', name: 'app_transaction_index', methods: ['GET'])]
	public function index(#[CurrentUser] ?User $user, TransactionRepository $transactionRepository, Request $request): Response
	{
		$query = $transactionRepository->getAllCurrentUserTransactionsQuery($user);
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
			$this->transactionService->amount($user, $transaction);
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
			$this->transactionService->editAmount($oldAmount, $user, $transaction);
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
			$this->user->decrementAmount($transaction->getAmount());
			$entityManager->remove($transaction);
			$entityManager->flush();
		}
		
		return $this->redirectToRoute('app_transaction_index', [], Response::HTTP_SEE_OTHER);
	}
}
