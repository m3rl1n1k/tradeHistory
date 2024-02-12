<?php

namespace App\Api\Controller;

use App\Api\Trait\SerializeTrait;
use App\Category\Entity\Category;
use App\Category\Repository\CategoryRepository;
use App\Entity\User;
use App\Transaction\Entity\Transaction;
use App\Transaction\Repository\TransactionRepository;
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
#[Route('/api/category')]
class CategoryApiController extends AbstractController
{
	use SerializeTrait;
	use TransactionTrait;
	
	public function __construct(protected SerializerInterface $serializer, protected CategoryRepository $categoryRepository)
	{
	}
	
	
	#[Route('/', name: 'app_api_category', methods: ['GET'])]
	public function category(#[CurrentUser] ?User $user, CategoryRepository $categoryRepository):
	JsonResponse
	{
		$user = $user->getUserId();
		$category = $categoryRepository->getAll($user);
		$content = $this->serializer($category, function: fn($category) => $category->getId());
		return $this->json($content)->setStatusCode(Response::HTTP_OK);
	}
	
	#[Route('/new', name: 'app_api_category_new', methods: ['POST'])]
	public function new(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager): JsonResponse
	{
		$content = $request->getContent();
		$category = $this->deserializer($content, Category::class);
		$category->setUser($user);
		$entityManager->persist($category);
		$entityManager->flush();
		return $this->json($category)->setStatusCode(Response::HTTP_OK);
	}
	
	#[Route('/edit/{id}', name: 'app_api_category_edit', methods: ['PUT'])]
	public function edit(#[CurrentUser] ?User $user, int $id, Request $request, EntityManagerInterface $entityManager): JsonResponse
	{
		/**
		 * @var Category $update
		 **/
		$category = $this->categoryRepository->getOneBy($id);
		$content = $request->getContent();
		$update = $this->deserializer($content, Category::class);
		$this->categoryRepository->categoryUpdate($category, $update, $user, $id);
		$entityManager->flush();
		$category = $this->serializer($category, function: fn($data) => $data->getId());
		return $this->json($category)->setStatusCode(Response::HTTP_OK);
		
	}
	
	#[Route('/delete/{id}', name: 'app_api_category_delete', methods: ['DELETE'])]
	public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
	{
		$category = $this->categoryRepository->getOneBy($id);
		
		$entityManager->remove($category);
		$entityManager->flush();
		
		return $this->json('Record remove!')->setStatusCode(Response::HTTP_OK);
	}
}