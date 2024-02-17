<?php

namespace App\Service;

use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartService
{
	public function __construct(protected ChartBuilderInterface $chartBuilder)
	{
	}
	
	public function dashboardChart(array $transactions)
	{
		$chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
		
		$chart->setData([
			'labels' => ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
			'datasets' => [
				[
					'label' => 'My First dataset',
					'backgroundColor' => 'rgb(255, 99, 132)',
					'borderColor' => 'rgb(255, 99, 132)',
					'data' => [0, 10, 5, 2, 20, 30, 45],
				],
			],
		]);
		
		$chart->setOptions([
			'scales' => [
				'y' => [
					'suggestedMin' => 0,
					'suggestedMax' => 100,
				],
			],
		]);
		return $chart;
	}
	
}