<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Repository\BudgetRepository;
use App\Repository\ParentCategoryRepository;
use App\Service\Budget\BudgetService;
use App\Trait\BudgetTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("IS_AUTHENTICATED_FULLY")]
#[Route('/budget')]
final class BudgetController extends AbstractController
{
    public function __construct(protected ParentCategoryRepository $parentCategoryRepository)
    {
    }

    use BudgetTrait;

    #[Route(name: 'app_budget_index', methods: ['GET'])]
    public function index(BudgetRepository $budgetRepository, BudgetService $budgetService): Response
    {
        $budgets = $budgetRepository->findBy(['user' => $this->getUser()], ['month' => 'DESC']);
        $budgets = $budgetService->summary($budgets);
        return $this->render('budget/index.html.twig', [
            'budgets' => $budgets,
        ]);
    }

    #[Route('/new', name: 'app_budget_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $budget = new Budget();
        $form = $this->getForm($budget);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $budget->setUser($this->getUser());
            $entityManager->persist($budget);
            $entityManager->flush();

            return $this->redirectToRoute('app_budget_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('budget/new.html.twig', [
            'budget' => $budget,
            'form' => $form,
        ]);
    }

    #[Route('/summary/{month}', name: 'app_budget_show', methods: ['GET'])]
    public function show(string $month, BudgetRepository $budgetRepository, BudgetService $budgetService): Response
    {
        $budget = $budgetRepository->findBy(['user' => $this->getUser(), 'month' => $month]);
        $budget = $budgetService->summary($budget, 'monthly');
        return $this->render('budget/show.html.twig', [
            'budget' => $budget,
            'date' => $month
        ]);
    }

    #[Route('/{id}/edit', name: 'app_budget_edit', methods: ['GET', 'POST'])]
    #[IsGranted("edit", 'budget')]
    public function edit(Request $request, Budget $budget, EntityManagerInterface $entityManager): Response
    {
        $form = $this->getForm($budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_budget_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('budget/edit.html.twig', [
            'budget' => $budget,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_budget_delete', methods: ['POST'])]
    public function delete(Request $request, Budget $budget, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $budget->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($budget);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_budget_index', [], Response::HTTP_SEE_OTHER);
    }
}