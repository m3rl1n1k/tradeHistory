<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use App\Transaction\TransactionEnum;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartService
{
    /**
     * @var Transaction[]
     */
    private array $transactions;

    public function __construct(protected ChartBuilderInterface $chartBuilder,
                                protected TransactionRepository $transactionRepository,
                                protected DateService           $dateService,
                                protected SettingService        $userSettings
    )
    {
        $this->transactions = $this->transactionRepository->getAllPerMonth();
    }

    public function reportChart(array $options): Chart
    {
        $chart = $this->create(Chart::TYPE_LINE);
        $dataset = $this->matchOptions($options);

        $chart->setData([
            'labels' => $this->dateService->currentMonthDates,
            'datasets' => $dataset,
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0
                ],
            ],
        ]);
        return $chart;
    }

    protected function create(string $type = Chart::TYPE_BAR): Chart
    {
        return $this->chartBuilder->createChart($type);
    }

    protected function matchOptions(array $options): array
    {
        if ($options['expense'])
            $res[] = $this->datasetReport(TransactionEnum::Expense->value, 'Expense');
        if ($options['income'])
            $res[] = $this->datasetReport(TransactionEnum::Profit->value, 'Income');
        return $res ?? [];
    }

    /**
     * @param string $type
     * @param string $label
     * @return array
     */
    protected function datasetReport(string $type, string $label): array
    {

//        $color = $this->colors($type);
        $result = $this->getSumByCurrentMonth($type);
        return [
            'label' => $label,
            'backgroundColor' => $this->colors($type),
            'borderColor' => $this->colors($type),
            'data' => $result,
        ];
    }

    /**
     * @param string $type
     * @return array
     */
    protected function getSumByCurrentMonth(string $type = TransactionEnum::Expense->value): array
    {
        $list = [];
        $days = $this->dateService->currentMonthDates;

        foreach ($days as $day) {
            $sum = null;
            $list[$day] = 0;
            foreach ($this->transactions as $transaction) {

                /** @var Transaction $transaction */
                $transactionType = $transaction->getType();
                $transactionDate = $transaction->getDate()->format('d M y');

                if ($transactionType == $type && $transactionDate == $day) {
                    $sum = $sum + $transaction->getAmount();
                    $list[$day] = $sum;
                }
            }
        }
        return $list;
    }

    protected function colors($type = ''): string
    {
        // == because $type return string and TransactionEnum::Expense->value return int
        if ($type == TransactionEnum::Expense->value) {
            return $this->userSettings::getSettings()['colorExpenseChart'];
        }
        if ($type == TransactionEnum::Profit->value) {
            return $this->userSettings->getSettings()['colorIncomeChart'];
        }
        $colorDataset = [
            'rgb(255, 99, 132, 0.6)',
            'rgb(225, 204, 079, 0.6)',
            'rgb(042, 046, 075, 0.6)',
            'rgb(230, 214, 144, 0.6)',
            'rgb(134, 115, 161, 0.6)',
            'rgb(037, 109, 123, 0.6)',
            'rgb(162, 035, 029, 0.6)',
            'rgb(048, 132, 070, 0.6)',
            'rgb(034, 113, 179, 0.6)',
            'rgb(234, 230, 202, 0.6)',
        ];
        return $colorDataset[array_rand($colorDataset)];
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function dashboardChart(): Chart
    {
        $chart = $this->create(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels' => $this->getCategoriesList(),
            'datasets' => [
                [
                    'data' => $this->datasetDashboard(TransactionEnum::Expense->value),
                    'backgroundColor' => $this->colors(),
                    'borderColor' => $this->colors(),
                    'borderWidth' => 1,
                ],
            ],
        ]);

// Set the chart options
        $chart->setOptions([
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'title' => [
                    'display' => true,
                    'text' => "Expense: {$this->totalExpense()}",
                    'font' => [
                        'size' => 20
                    ],
                ]
            ],
        ]);

        return $chart;
    }

    public function getCategoriesList(): array
    {
        $list = [];
        $list[] = "No category";
        foreach ($this->transactions as $transaction) {
            $category = $transaction->getCategory();
            if ($category !== null)
                $list[] = $transaction->getCategory()->getName();
        }
        return array_values(array_unique($list));
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    protected function datasetDashboard(int $type): array
    {
        $dataset = [];
        $dataset['no_category'] = 0;
        foreach ($this->transactions as $transaction) {
            $category = $transaction->getCategory();
            if ($transaction->getType() === $type && $category !== null) {
                $dataset[$category->getId()] = $this->transactionRepository->getTransactionSum([
                    'category' => $category->getId()
                ]);
            } elseif ($transaction->getType() === $type) {
                $dataset['no_category'] += $transaction->getAmount();
            }
        }
        return array_values($dataset);
    }

    protected function totalExpense(): float
    {
        $sum = 0;
        foreach ($this->transactions as $transaction) {
            if ($transaction->getType() === TransactionEnum::Expense->value)
                $sum += $transaction->getAmount();
        }
        return round($sum, 2);
    }

    public function historyChart(array $transactionsHistory): Chart
    {
        $chart = $this->create();
        $chart->setData([
            'labels' => array_keys($transactionsHistory),
            'datasets' => [
                [
                    'data' => array_values($transactionsHistory),
                    'backgroundColor' => $this->colors(),
                    'borderColor' => $this->colors(),
                    'borderWidth' => 1,
                ],
            ],
        ]);

        $chart->setOptions([
            'plugins' => [
                'legend' => [
                    'display' => false,
                ]
            ],
        ]);
        return $chart;
    }


}