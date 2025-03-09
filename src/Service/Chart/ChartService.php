<?php

namespace App\Service\Chart;

use App\Entity\Transaction;
use App\Enum\TransactionTypeEnum;
use App\Repository\TransactionRepository;
use App\Service\DateService;
use App\Service\SettingService;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChartService
{
    /**
     * @var Transaction[]
     */
    private array $transactions;

    public function __construct(protected TransactionRepository $transactionRepository,
                                protected DateService           $dateService,
                                protected SettingService        $userSettings,
                                protected TranslatorInterface   $translator
    )
    {
        $this->transactions = $this->transactionRepository->getTransactionForCurrentMonth();
    }

    public function dashboardChart()
    {
        $chartDataset = new ChartDataset($this->translator);
        $this->datasetDashboard($chartDataset, ['type' => TransactionTypeEnum::Expense->value]);
        return [
            'data' => [
                'labels' => json_encode($chartDataset->getLabels()),
                'datasets' => [
                    'data' => json_encode($chartDataset->getAmounts()),
                    'backgroundColor' => json_encode($chartDataset->getColors()),
                    'borderWidth' => 1,
                ],
            ],
            'expense' => $this->totalExpense()];
    }

    protected function datasetDashboard(ChartDataset $chartDataset, array $options = []): void
    {
        $type = $options['type'];
        foreach ($this->transactions as $transaction) {
            $category = $transaction->getCategory();
            if ($category === null && $transaction->getType() === $type) {
                $chartDataset->setData('chart.no_category', $transaction->getAmount());
                $chartDataset->setColor(null);
            }
            if ($category !== null && $transaction->getType() === $type) {
                $parent = $category->getParentCategory();

                $chartDataset->setColor($parent->getColor());

                $amount = $this->transactionRepository->calculateSum($this->transactions, ['category' => $category->getId()]);
                $chartDataset->setData($parent->getName(), $amount);
            }
        }
    }

    private function totalExpense(): ?float
    {
        return $this->transactionRepository->getTotalExpenseByMonth($this->transactions);
    }
}