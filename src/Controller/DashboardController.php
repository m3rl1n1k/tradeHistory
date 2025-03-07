<?php

namespace App\Controller;

use App\Repository\BudgetRepository;
use App\Repository\TransactionRepository;
use App\Service\Budget\BudgetService;
use App\Service\ChartService;
use App\Service\WalletService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class DashboardController extends AbstractController
{
    public function __construct(
        protected ChartService     $chartService,
        protected BudgetRepository $budgetRepository,
        protected BudgetService    $budgetService,
        protected WalletService    $walletService,
    )
    {
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/{_locale}/home', name: 'app_home', methods: ['GET'])]
    public function home(TransactionRepository $transactionRepository): Response
    {
        $data = $this->chartService->dashboardChart();
        $labels = $data['labels'];
        $chartData = $data['datasets']['data'];
        $colors = $data['datasets']['backgroundColor'];
        $firstDayOfMonth = new DateTime('now');
        $budget = $this->budgetRepository->findBy(['user' => $this->getUser(), 'month' => $firstDayOfMonth->format('Y-m-01')]);
        return $this->render('dashboard/index.html.twig', [
            'current_budget' => $this->budgetService->summary($budget),
            'last_transaction' => $transactionRepository->getLastTransaction(),
            'labels' => json_encode($labels, JSON_PRETTY_PRINT),
            'data' => json_encode($chartData, JSON_PRETTY_PRINT),
            'colors' => json_encode($colors, JSON_PRETTY_PRINT),
            'expense_amount' => $data['expense'],
            'amount' => $this->walletService->getTotal()
        ]);
    }
}