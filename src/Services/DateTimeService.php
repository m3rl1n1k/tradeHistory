<?php

namespace App\Services;

class DateTimeService
{
	public function calculateDifferent(string $different): \DateTime
	{
		$date = new \DateTime();
		$date->modify("+$different");
		return $date;
	}

}