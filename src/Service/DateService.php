<?php

namespace App\Service;

use DateInterval;
use DatePeriod;
use DateTime;

class DateService
{
	
	public function getCurrentDate(bool $getMonth = false): array|DatePeriod
	{
		$currentYear = date("Y");
		$currentMonth = date("m");
		
		$start = new DateTime("$currentYear-$currentMonth-01");
		$end = new DateTime("$currentYear-$currentMonth-01");
		$end->modify('last day of this month');
		$interval = new DateInterval('P1D');
		$month = new DatePeriod($start, $interval, $end);
		if ($getMonth) {
			return $month;
		}
		$daysArray = [];
		foreach ($month as $date) {
			$daysArray[] = $date->format('d M y');
		}
		
		return $daysArray;
	}
}