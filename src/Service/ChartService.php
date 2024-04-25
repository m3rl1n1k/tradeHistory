<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Repository\TransactionRepository;
use App\Transaction\TransactionEnum;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;
use function PHPUnit\Framework\arrayHasKey;

class ChartService
{
    /**
     * @var Transaction[]
     */
    private array $transactions;

    public function __construct(protected ChartBuilderInterface $chartBuilder,
                                protected TransactionRepository $transactionRepository,
                                protected DateService           $dateService
    )
    {
        $this->transactions = $this->transactionRepository->getAllPerCurrentMonth();
    }

    public function reportChart(array $options): Chart
    {
        $dataset = [];
        $chart = $this->create(Chart::TYPE_LINE);
        if ($options['expense'])
            $dataset[] = $this->datasetReport(TransactionEnum::Expense->value, 'Expense', 0);
        if ($options['income'])
            $dataset[] = $this->datasetReport(TransactionEnum::Income->value, 'Income', 1);

        $chart->setData([
            'labels' => $this->dateService->getCurrentDate(),
            'datasets' => $dataset,
        ]);

        $chart->setOptions([
            'scales' => [
                'y' => [
                    'suggestedMin' => 0,
                    'suggestedMax' => 100
                ],
            ],
        ]);
        return $chart;
    }

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
                ]
            ],
        ]);

        return $chart;
    }

    /**
     * @param string $type
     * @param string $label
     * @param int $colorMax10
     * @return array
     */
    protected function datasetReport(string $type, string $label = '', int $colorMax10 = 10): array
    {
        $result = $this->getSumByCurrentMonth($type);
        return [
            'label' => $label,
            'backgroundColor' => $this->colors()[$colorMax10],
            'borderColor' => $this->colors()[$colorMax10],
            'data' => $result ?? [],
        ];
    }

    protected function colors(): array
    {
        return [
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
    }


    /**
     * @param string $type
     * @return array
     */
    protected function getSumByCurrentMonth(string $type = TransactionEnum::Expense->value): array
    {
        $list = [];
        $days = $this->dateService->getCurrentDate();

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
        $dataset = [];
        foreach ($this->transactions as $transaction) {
            $subCategory = $transaction->getSubCategory();
            if ($transaction->getType() === $type) {
                $dataset[$subCategory->getId()] = $this->transactionRepository->getTransactionSum([
                    'subCategory' => $subCategory->getId()
                ]);
            }
        }
        return $dataset;
    }

    public function getCategoriesList(): array
    {
        $list = [];
        foreach ($this->transactions as $transaction) {
            $subCategory = $transaction->getSubCategory();
            if (!$subCategory) {
                break;
            }
            $name = $subCategory->getName();
            if (!array_keys($list, $name))
                $list[] = $transaction->getSubCategory()->getName();
        }
        return $list;
    }


}