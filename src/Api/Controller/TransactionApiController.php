<?php

namespace App\Api\Controller;

use App\Api\Trait\SerializeTrait;
use App\Entity\User;
use App\Transaction\Entity\Transaction;
use App\Transaction\Repository\TransactionRepository;
use App\Transaction\Sevice\TransactionService;
use App\Transaction\Trait\TransactionTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/api/transaction')]
class TransactionApiController extends AbstractController
{
	use SerializeTrait;
	use TransactionTrait;
	
	public function __construct(protected SerializerInterface $serializer, protected TransactionService $transactionService)
	{
	}
	
	
	#[Route('/', name: 'app_api_transaction', methods: ['GET'])]
	public function transaction(#[CurrentUser] ?User $user, TransactionRepository $transactionRepository):
	JsonResponse
	{
		$user = $user->getUserId();
		$data = $transactionRepository->getTransactionsApi($user);
		$data = $this->serializer($data, function: fn($data) => $data->getId());
		return $this->json($data)->setStatusCode(Response::HTTP_OK);
	}
	
	#[Route('/new', name: 'app_api_transaction_new', methods: ['POST'])]
	public function new(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager): JsonResponse
	{
		$data = $request->getContent();
		/**
		 * @var Transaction $data
		 **/
		$data = $this->deserializer($data, Transaction::class);
		$data->setUserId($user);
		$entityManager->persist($data);
		$entityManager->flush();
		return $this->json($data)->setStatusCode(Response::HTTP_OK);
	}
	
	#[Route('/show/{id}', name: 'app_api_transaction_show', methods: ['GET'])]
	public function get(int $id, TransactionRepository $transactionRepository): JsonResponse
	{
		$transaction = $transactionRepository->getOneBy($id);
		$transaction = $this->serializer($transaction, function: fn($data) => $data->getId());
		return $this->json($transaction)->setStatusCode(Response::HTTP_OK);
	}
	
	#[Route('/edit/{id}', name: 'app_api_transaction_edit', methods: ['PUT'])]
	public function edit(#[CurrentUser] ?User   $user, int $id, Request $request, TransactionRepository $transactionRepository,
						 EntityManagerInterface $entityManager):
	JsonResponse
	{
		/**
		 * @var Transaction $content
		 **/
		$transaction = $transactionRepository->getOneBy($id);
		$oldAmount = $transaction->getAmount();
		$content = $request->getContent();
		$update = $this->deserializer($content, Transaction::class);
		$update->setUserId($user);
		$this->transactionService->editAmount($oldAmount, $user, $update);
		$entityManager->flush();
		return $this->json($update)->setStatusCode(Response::HTTP_OK);
		
	}
	
	#[Route('/delete/{id}', name: 'app_api_transaction_delete', methods: ['DELETE'])]
	public function delete()
	{
	
	}
}