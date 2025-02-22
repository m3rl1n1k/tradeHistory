<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Enum\TransactionTypeEnum;
use App\Repository\TransactionRepository;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartService
{
    /**
     * @var Transaction[]
     */
    private array $transactions;
    private ?string $withoutCategory = null;

    public function __construct(protected TransactionRepository $transactionRepository,
                                protected DateService           $dateService,
                                protected SettingService        $userSettings
    )
    {
        $this->transactions = $this->transactionRepository->getTransactionForCurrentMonth();
    }

    public function dashboardChart(): array
    {
        $data = $this->datasetDashboard(TransactionTypeEnum::Expense->value);
        $dataset = $data['dataset'];
        $colors = $data['colors'];
        $labels = $this->getCategoriesList();
        return [
            'labels' => $labels,
            'datasets' => [
                    'data' => $dataset,
                    'backgroundColor' => $colors,
                    'borderWidth' => 1,
            ],
            'expense' => $this->totalExpense()
        ];
    }

    protected function datasetDashboard(int $type): array
    {
        $data = $colors = [];
        $data['without_category'] = null;
        foreach ($this->transactions as $transaction) {
            $category = $transaction->getCategory();
            if ($category !== null && $transaction->getType() === $type) {
                $colors[$category->getId()] = $category->getColor() ?? "#eeeeee";
                $categoryId = $category->getId();
                $data[$categoryId] = $this->transactionRepository->calculateSum($this->transactions, ['category' => $categoryId]);
            } elseif ($transaction->getType() === $type) {
                $this->withoutCategory = $data['without_category'] += $transaction->getAmount();
                $colors['without_category'] = "#3a3a3a";
            }
        }
        if ($data['without_category'] === null) {
            unset($data['without_category']);
        }

        return [
            'dataset' => array_values($data),
            'colors' => array_values($colors)
        ];
    }

    public function getCategoriesList(): array
    {
        $list = [];
        if ($this->withoutCategory !== null) {
            $list['without_category'] = "Without Category";
        }
        foreach ($this->transactions as $transaction) {
            $category = $transaction->getCategory();
            if ($category !== null && $transaction->getType() === TransactionTypeEnum::Expense->value)
                $list[] = $transaction->getCategory()->getName();
        }
        return array_values(array_unique($list));
    }

    private function totalExpense(): ?float
    {
        return $this->transactionRepository->getTotalExpenseByMonth($this->transactions);
    }

    protected function colors($type = ''): string
    {
        if ((int)$type === TransactionTypeEnum::Expense->value) {
            return $this->userSettings::getColorExpenseChart();
        }
        if ((int)$type === TransactionTypeEnum::Profit->value) {
            return $this->userSettings::getColorIncomeChart();
        }
        return "";
    }
}