<?php

namespace App\Controller;

use App\Entity\Budget;
use App\Form\BudgetType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/budget')]
final class BudgetController extends AbstractController
{
    #[Route(name: 'app_budget_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('pages/budget/index.html.twig', [
            'budgets' => $this->getUser()->getBudgets(),
        ]);
    }

    #[Route('/new', name: 'app_budget_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $budget = new Budget();
        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $budget->setUser($this->getUser());
            $entityManager->persist($budget);
            $entityManager->flush();

            return $this->redirectToRoute('app_budget_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/budget/new.html.twig', [
            'budget' => $budget,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_budget_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Budget $budget, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BudgetType::class, $budget);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_budget_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pages/budget/edit.html.twig', [
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
