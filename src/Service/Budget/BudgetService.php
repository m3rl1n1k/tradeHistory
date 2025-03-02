<?php

namespace App\Service\Budget;

use App\Entity\Budget;
use App\Repository\BudgetRepository;
use App\Repository\TransactionRepository;
use DateTime;

class BudgetService
{
    public function __construct(
        protected TransactionRepository $transactionRepository,
        protected BudgetRepository      $budgetRepository
    )
    {
    }

    public function summary(array|Budget $budgets, string $type = 'yearly', array $options = []): array|BudgetSummary
    {
        return match ($type) {
            'yearly' => $this->summaryYearly($budgets, $options),
            'monthly' => $this->summaryMonthly($budgets, $options),
        };
    }

    private function summaryYearly(array $budgets, array $options = []): array
    {
        $summary = [];

        foreach ($budgets as $budget) {
            /** @var Budget $budget */
            $budgetDate = $budget->getMonth();
            $transactionsSumForCategory = $this->getTransactionsSumForCategory($budget, $budgetDate);
            if (!isset($summary[$budgetDate])) {
                $summary[$budgetDate] = new BudgetSummary();
            }
            $summary[$budgetDate]->updateTotals($budget->getPlannedAmount(), $transactionsSumForCategory);
        }

        return $summary;
    }

    private function getTransactionsSumForCategory(Budget $budget, string $budgetDate): float
    {
        $budgetCategory = $budget->getCategory();

        $startDay = new DateTime("{$budgetDate} 00:00:00");
        $lastDay = new DateTime("last day of {$budgetDate} 23:59:59");

        $transactions = $this->transactionRepository->getTransactionsPerPeriodByCategory(
            $budgetCategory,
            $startDay,
            $lastDay
        );
        // same as foreach
        return array_reduce($transactions, fn($sum, $t) => $sum + $t->getAmount(), 0);
    }

    private function summaryMonthly(array $budgets, array $options = []): BudgetSummary
    {
        $summary = [];

        foreach ($budgets as $budget) {
            $budgetDate = $budget->getMonth();
            $transactionsSumForCategory = $this->getTransactionsSumForCategory($budget, $budgetDate);
            if (!isset($summary[$budgetDate])) {
                $summary[$budgetDate] = new BudgetSummary();
            }
            $categoryData = [
                'name' => $budget->getCategory()->getName(),
                'plannedAmount' => $budget->getPlannedAmount(),
                'actualAmount' => $transactionsSumForCategory,
                'budgetId' => $budget->getId()
            ];
            $summary[$budgetDate]->addCategory($budget->getCategory()->getId(), $categoryData);
            $summary[$budgetDate]->updateTotals($budget->getPlannedAmount(), $transactionsSumForCategory);
        }
        return $summary[$budgetDate];
    }

}