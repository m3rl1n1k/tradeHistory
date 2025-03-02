<?php

namespace App\Service\Budget;

class BudgetSummary
{
    protected float $totalPlanned = 0;
    protected float $totalActual = 0;
    protected array $categories = [];

    public function addCategory(int $categoryId, array $data): void
    {
        $this->categories[$categoryId] = $data;
    }

    public function updateTotals(float $planned, float $actual): void
    {
        $this->totalPlanned += $planned;
        $this->totalActual += $actual;
    }

    public function getTotalPlanned(): float
    {
        return $this->totalPlanned;
    }

    public function getTotalActual(): float
    {
        return $this->totalActual;
    }

    public function getTotalRemaining(): float
    {
        return $this->totalPlanned - $this->totalActual;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }
}