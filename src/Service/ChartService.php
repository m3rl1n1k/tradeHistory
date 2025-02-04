<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Enum\TransactionEnum;
use App\Repository\TransactionRepository;
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

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function dashboardChart(): Chart
    {
        $chart = $this->create(Chart::TYPE_DOUGHNUT);
        $data = $this->datasetDashboard(TransactionEnum::Expense->value);
        $dataset = $data['dataset'];
        $colors = $data['colors'];
//        dd($data, $dataset, $colors);

        $chart->setData([
            'labels' => $this->getCategoriesList(),
            'datasets' => [
                [
                    'data' => $dataset,
                    'backgroundColor' => $colors,
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
//                'title' => [
//                    'display' => true,
//                    'text' => "Expense: {$this->totalExpense()}",
//                    'font' => [
//                        'size' => 20
//                    ],
//                ]
            ],
        ]);

        return $chart;
    }

    protected function create(string $type = Chart::TYPE_BAR): Chart
    {
        return $this->chartBuilder->createChart($type);
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    protected function datasetDashboard(int $type): array
    {
        $dataset = $colors = [];
        $dataset['no_category'] = null;
        foreach ($this->transactions as $transaction) {
            $category = $transaction->getCategory();
            if ($category !== null) {
                $colors[] = $category->getColor();
            } else {
                $colors[] = null;
            }
            if ($transaction->getType() === $type && $category !== null) {
                $dataset[$category->getId()] = $this->transactionRepository->getTransactionSum([
                    'category' => $category->getId(),
                ]);
            } elseif ($transaction->getType() === $type) {
                $dataset['no_category'] += $transaction->getAmount();
            }
        }
        
        return [
            'dataset' => array_values($dataset),
            'colors' => $colors,
        ];
    }

    public function getCategoriesList(): array
    {
        $list = [];
        $list[] = "No Category";
        foreach ($this->transactions as $transaction) {
            $category = $transaction->getCategory();
            if ($category !== null)
                $list[] = $transaction->getCategory()->getName();
        }
        return array_values(array_unique($list));
    }

    protected function colors($type = ''): string
    {
        if ((int)$type === TransactionEnum::Expense->value) {
            return $this->userSettings::getColorExpenseChart();
        }
        if ((int)$type === TransactionEnum::Profit->value) {
            return $this->userSettings::getColorIncomeChart();
        }
        return "";
    }
}