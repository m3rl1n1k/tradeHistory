<?php

namespace App\Controller;

use App\Entity\ParentCategory;
use App\Form\ParentCategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ParentCategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/parent/category')]
final class ParentCategoryController extends AbstractController
{
    public function __construct(protected CategoryRepository       $CategoryRepository,
                                protected ParentCategoryRepository $parentCategoryRepository)
    {
    }

    #[Route('/', name: 'app_parent_category_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('parent_category/index.html.twig', [
            'parent_categories' => $this->parentCategoryRepository->getMainAndSubCategories(),
        ]);
    }

    #[Route('/new', name: 'app_parent_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parentCategory = new ParentCategory();
        $form = $this->createForm(ParentCategoryType::class, $parentCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $parentCategory->setUser($this->getUser());
            $entityManager->persist($parentCategory);
            $entityManager->flush();
            $this->addFlash('success', 'Category created.');
            return $this->redirectToRoute('app_parent_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('parent_category/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_parent_category_delete', methods: ['POST'])]
    public function delete(Request $request, ParentCategory $parentCategory, EntityManagerInterface $entityManager):
    Response
    {
        if ($this->isCsrfTokenValid('delete' . $parentCategory->getId(), $request->request->get('_token'))) {
            try {
                $entityManager->beginTransaction();
                foreach ($this->CategoryRepository->getAll($parentCategory->getId()) as $category) {
                    $entityManager->remove($category);
                }

                $entityManager->remove($parentCategory);
                $entityManager->commit();
                $entityManager->flush();
            } catch (Exception $e) {
                echo($e->getMessage());
                $entityManager->rollback();
            }
        }

        return $this->redirectToRoute('app_parent_category_index', [], Response::HTTP_SEE_OTHER);
    }
}