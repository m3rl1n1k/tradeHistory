<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ParentCategoryRepository;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/category')]
class CategoryController extends AbstractController
{
    private array $parentCategories;

    public function __construct(
        protected ParentCategoryRepository $parentCategoryRepository,
        protected CategoryRepository       $category,
        protected CategoryService          $categoryService
    )
    {
        $this->parentCategories = $this->parentCategoryRepository->getMainAndSubCategories();
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category, [
            'parent_categories' => $this->parentCategories
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->validateSimilarName(Category::class, $category, $entityManager);
            $this->categoryService->mainColor($category, $form);
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_parent_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'category')]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager):
    Response
    {
        $form = $this->createForm(CategoryType::class, $category, [
            'parent_categories' => $this->parentCategories
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->validateSimilarName(Category::class, $category, $entityManager);
            $entityManager->flush();

            return $this->redirectToRoute('app_parent_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_parent_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
