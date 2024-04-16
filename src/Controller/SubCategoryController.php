<?php

namespace App\Controller;

use App\Entity\SubCategory;
use App\Entity\User;
use App\Form\SubCategoryType;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/sub/category')]
class SubCategoryController extends AbstractController
{
	public function __construct(
		protected CategoryRepository $category,
		protected SubCategoryRepository $subCategory
	)
	{
	}
	
	#[Route('/new', name: 'app_sub_category_new', methods: ['GET', 'POST'])]
	public function new(Request $request, EntityManagerInterface $entityManager): Response
	{
		$subCategory = new SubCategory();
		$form = $this->createForm(SubCategoryType::class, $subCategory, [
			'main_category' => $this->category->getAll()
		]);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
            $this->subCategory->isSimilar($subCategory);
			$entityManager->persist($subCategory);
			$entityManager->flush();
			
			return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
		}
		
		return $this->render('sub_category/new.html.twig', [
			'sub_category' => $subCategory,
			'form' => $form,
		]);
	}
	
	#[Route('/{id}/edit', name: 'app_sub_category_edit', methods: ['GET', 'POST'])]
	public function edit(Request $request, SubCategory $subCategory, EntityManagerInterface $entityManager):
	Response
	{
		$form = $this->createForm(SubCategoryType::class, $subCategory, [
			'main_category' => $this->category->getAll()
		]);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->flush();
			
			return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
		}
		
		return $this->render('sub_category/edit.html.twig', [
			'sub_category' => $subCategory,
			'form' => $form,
		]);
	}
	
	#[Route('/{id}', name: 'app_sub_category_delete', methods: ['POST'])]
	public function delete(Request $request, SubCategory $subCategory, EntityManagerInterface $entityManager): Response
	{
		if ($this->isCsrfTokenValid('delete' . $subCategory->getId(), $request->request->get('_token'))) {
			$entityManager->remove($subCategory);
			$entityManager->flush();
		}
		
		return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
	}
}
