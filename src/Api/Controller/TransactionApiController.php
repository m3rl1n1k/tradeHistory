<?php

namespace App\Api\Controller;

use App\Api\Trait\SerializeTrait;
use App\Entity\User;
use App\Transaction\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/api/transaction')]
class TransactionApiController extends AbstractController
{
	use SerializeTrait;
	
	#[Route('/', name: 'app_api_transactionApi', methods: ['GET'])]
	public function transaction(#[CurrentUser] ?User $user, TransactionRepository $transactionRepository):
	JsonResponse
	{
		$user = $user->getUserId();
		$data = $transactionRepository->getTransactionsApi($user);
		$data = $this->serializer($data, function: fn($data) => $data->getId());
		return $this->json($data);
	}
	
	#[Route('/new', name: 'app_api_transactionApi_new', methods: ['POST'])]
	public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
	{
		$data = $request->request;
		dd($data);
		$data = $this->deserializer($data);
		return new JsonResponse('Created Successfully!');
	}
	
	#[Route('/{id}', name: 'app_api_transactionApi_get', methods: ['GET'])]
	public function get(int $id, TransactionRepository $transactionRepository): JsonResponse
	{
		$data = $transactionRepository->findBy(['id' => $id]);
		$data = $this->serializer($data, function: fn($data) => $data->getId());
		return new JsonResponse($data);
	}
	
	#[Route('/edit/{id}', name: 'app_api_transactionApi_edit', methods: ['GET', 'POST'])]
	public function edit(int $id)
	{
	
	}
	
	#[Route('/delete/{id}', name: 'app_api_transactionApi_delete', methods: ['GET', 'POST'])]
	public function delete()
	{
	
	}
}