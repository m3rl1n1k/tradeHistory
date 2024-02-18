<?php

namespace App\Chart\Service;

use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Transaction\Repository\TransactionRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use JetBrains\PhpStorm\NoReturn;
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
			'labels' => array_values($this->dataset($user, categoryReturn: true)),
			'datasets' => [
				$this->dataset($user, $label)
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
		return $this->transactionRepository->getSumByCategory($id) ?? 0;
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
	protected function dataset($user, string $label = '', bool $categoryReturn = false): array
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
			return $categoriesList;
		}
		return [
			'label' => $label,
			'backgroundColor' => $this->colors(),
			'borderColor' => $this->colors(),
			'data' => $result,
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
	#[NoReturn] public function debug($user)
	{
		$categories = $this->getCategories($user);
		$data = $this->dataset($categories, '');
		dd($data);
	}
	
}