<?php

namespace App\Chart\Service;

use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Transaction\Enum\TransactionEnum;
use App\Transaction\Repository\TransactionRepository;
use DateInterval;
use DatePeriod;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartService
{
	public function __construct(protected ChartBuilderInterface $chartBuilder,
								protected CategoryRepository    $categoryRepository,
								protected TransactionRepository $transactionRepository
	)
	{
	}
	
	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	public function dashboardChart(User $user, string $label): Chart
	{
		$chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
		$chart->setData([
			'labels' => array_values($this->datasetDashboard($user, categoryReturn: true)),
			'datasets' => [
				$this->datasetDashboard($user, $label)
			],
		]);
		
		$chart->setOptions([
			'scales' => [
				'y' => [
					'suggestedMin' => 0,
					'suggestedMax' => $this->getMax($user),
				],
			],
		]);
		return $chart;
	}
	
	protected function getCategories($user): array
	{
		$categoryList = [];
		$categories = $this->categoryRepository->getCategories($user->getUserId());
		foreach ($categories as $category) {
			$categoryList[$category->getId()] = $category->getName();
		}
		return $categoryList;
	}
	
	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	protected function getSumByCategory(int $id): bool|float|int|string
	{
		return $this->transactionRepository->getTransactionSum(['category' => $id]) ?? 0;
	}
	
	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	protected function getMax(User $user): float
	{
		return (float)$this->transactionRepository->getMaxAmount($user->getUserId());
	}
	
	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	protected function datasetDashboard($user, string $label = '', bool $categoryReturn = false, bool $singleColor =
	false):
	array
	{
		$categories = $this->getCategories($user);
		foreach ($categories as $key => $category) {
			$sum = $this->getSumByCategory($key);
			if ($sum) {
				$categoriesList[] = $category;
				$result[] = $sum;
			}
		}
		if ($categoryReturn) {
			return $categoriesList ?? [];
		}
		return [
			'label' => $label,
			'backgroundColor' => $this->colors(),
			'borderColor' => $singleColor ? $this->colors()[0] : $this->colors(),
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
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	public function reportChart(User $user): Chart
	{
		//select all transactions per month and build chart
		$chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
		$chart->setData([
			'labels' => $this->getDateArray(),
			'datasets' => [
				$this->datasetReport(TransactionEnum::EXPENSE, 'Expense', 0),
				$this->datasetReport(TransactionEnum::INCOME, 'Income',1)
			],
		]);
		
		$chart->setOptions([
			'scales' => [
				'y' => [
					'suggestedMin' => 0,
					'suggestedMax' => $this->getMax($user),
				],
			],
		]);
		return $chart;
	}
	
	private function getDateArray(): array
	{
		$currentYear = date("Y");
		$currentMonth = date("m");
		
		$start = new DateTime("$currentYear-$currentMonth-01");
		$end = new DateTime("$currentYear-$currentMonth-01");
		$end->modify('last day of this month');
		$interval = new DateInterval('P1D');
		$period = new DatePeriod($start, $interval, $end);
		
		$daysArray = [];
		foreach ($period as $date) {
			$daysArray[] = $date->format('y-m-d');
		}
		
		return $daysArray;
	}
	
	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	private function getSumByDay(array $days, string $type): array
	{
		$sum = [];
		foreach ($days as $day) {
			$sum[] = $this->transactionRepository->getTransactionSum(['date' => $day], $type) ?? 0;
		}
		return $sum;
	}
	
	/**
	 * @throws NonUniqueResultException
	 * @throws NoResultException
	 */
	protected function datasetReport(string $type, string $label = '', int $colorMax10 = 10):
	array
	{
		$days = $this->getDateArray();
		$result = $this->getSumByDay($days, $type);
		return [
			'label' => $label,
			'backgroundColor' => $this->colors()[$colorMax10],
			'borderColor' => $this->colors()[$colorMax10],
			'data' => $result ?? [],
		];
	}
	
}