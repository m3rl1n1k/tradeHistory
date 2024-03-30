<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\SubCategory;
use App\Entity\User;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\SubCategoryRepository;
use App\Trait\AccessTrait;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/category')]
class CategoryController extends AbstractController
{
	public function __construct(protected SubCategoryRepository $subCategoryRepository,
								protected CategoryRepository    $categoryRepository)
	{
	}
	
	use AccessTrait;
	
	#[Route('/', name: 'app_category_index', methods: ['GET'])]
	public function index(): Response
	{
		return $this->render('category/index.html.twig', [
			'categories' => $this->categoryRepository->getMainAndSubCategories(),
		]);
	}
	
	#[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
	public function new(#[CurrentUser] ?User $user, Request $request, EntityManagerInterface $entityManager): Response
	{
		$category = new Category();
		$form = $this->createForm(CategoryType::class, $category);
		$form->handleRequest($request);
		
		if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryRepository->isSimilar($category);
			$category->setUser($user);
			$entityManager->persist($category);
			$entityManager->flush();
			
			return $this->redirectToRoute('app_category_new', [], Response::HTTP_SEE_OTHER);
		}
		
		$categories = $this->categoryRepository->getAll();
		return $this->render('category/new.html.twig', [
			'categories' => $categories,
			'category' => $category,
			'form' => $form,
		]);
	}
	
	#[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
	public function delete(#[CurrentUser] ?User $user, Request $request, Category $category, EntityManagerInterface $entityManager):
	Response
	{
		$this->accessDenied($category, $user);
		if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
			
			try {
				$entityManager->beginTransaction();
				foreach ($this->subCategoryRepository->getAll($category->getId()) as $subCategory) {
					$entityManager->remove($subCategory);
				}
				
				$entityManager->remove($category);
				$entityManager->commit();
				$entityManager->flush();
			} catch (Exception $e) {
				echo($e->getMessage());
				$entityManager->rollback();
			}
		}
		
		return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
	}
}
