<?php

namespace App\Controller;

use App\Entity\ParentCategory;
use App\Entity\User;
use App\Form\ParentCategoryType;
use App\Repository\CategoryRepository;
use App\Repository\ParentCategoryRepository;
use App\Trait\AccessTrait;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/parent/category')]
class ParentCategoryController extends AbstractController
{
    protected ?User $user;

    public function __construct(protected CategoryRepository       $CategoryRepository,
                                protected ParentCategoryRepository $parentCategoryRepository,
                                protected Security                 $security)
    {
        $this->user = $this->security->getUser();
    }

    use AccessTrait;

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
        $category = new ParentCategory();
        $form = $this->createForm(ParentCategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->parentCategoryRepository->isSimilar($category);
            $category->setUser($this->user);
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_parent_category_new', [], Response::HTTP_SEE_OTHER);
        }

        $parentCategories = $this->parentCategoryRepository->getAll();
        return $this->render('parent_category/new.html.twig', [
            'parent_categories' => $parentCategories,
            'categories' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_parent_category_delete', methods: ['POST'])]
    public function delete(Request $request, ParentCategory $category, EntityManagerInterface $entityManager):
    Response
    {
        $this->accessDenied($category, $this->user);
        if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {

            try {
                $entityManager->beginTransaction();
                foreach ($this->CategoryRepository->getAll($category->getId()) as $Category) {
                    $entityManager->remove($Category);
                }

                $entityManager->remove($category);
                $entityManager->commit();
                $entityManager->flush();
            } catch (Exception $e) {
                echo($e->getMessage());
                $entityManager->rollback();
            }
        }

        return $this->redirectToRoute('app_parent_category_new', [], Response::HTTP_SEE_OTHER);
    }
}
