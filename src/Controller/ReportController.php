<?php

namespace App\Controller;

use App\Enum\TransactionTypeEnum;
use App\Form\ReportPeriodType;
use App\Service\ChartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class ReportController extends AbstractController
{
    public function __construct(
        protected ChartService $chartService,
    )
    {
    }


    #[Route('/report', name: 'app_report', methods: ['GET', 'POST'])]
    public function index(Request $request):
    Response
    {
        $incomeCheck = $request->query->get('income', false);
        $expenseCheck = $request->query->get('expense', false);
        $form = $this->createForm(ReportPeriodType::class);
        $form->handleRequest($request);

        $chart = $this->chartService->reportChart([
            'income' => $incomeCheck,
            'expense' => $expenseCheck
        ]);

        if ($form->isSubmitted() && $form->isValid()) {
            $formDate = $form->getData();

            $start = $formDate['dateFrom'];
            $end = $formDate['dateEnd'];


            $transactions = $this->transactionService->getTransactionsPerPeriod($start, $end);
            $income = $this->transactionService->getSum($transactions, TransactionTypeEnum::Profit->value);
            $expense = $this->transactionService->getSum($transactions, TransactionTypeEnum::Expense->value);
            $transactionsHistory = $this->transactionService->groupTransactionsByCategory($transactions);
            $historyChart = $this->chartService->historyChart($transactionsHistory);

            return $this->render('report/index.html.twig', [
                'income' => $income,
                'expense' => $expense,
                'form' => $form,
                'transactionsList' => $historyChart,
                'chart' => $chart,
                'expense_check' => $expenseCheck,
                'income_check' => $incomeCheck
            ]);
        }
        return $this->render('report/index.html.twig', [
            'income' => null,
            'expense' => null,
            'form' => $form,
            'transactionsList' => null,
            'chart' => $chart,
            'expense_check' => $expenseCheck,
            'income_check' => $incomeCheck
        ]);
    }

}