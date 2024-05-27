<?php

namespace App\Service;

use DateInterval;
use DatePeriod;
use DateTime;

class DateService
{
    public DatePeriod|array $currentMonthDates;

    public function __construct()
    {
        $this->currentMonthDates = $this->getCurrentMonthDates();
    }

    protected function getCurrentMonthDates(): array|DatePeriod
    {
        $currentYear = date("Y");
        $currentMonth = date("m");

        $start = new DateTime("$currentYear-$currentMonth-01");
        $end = new DateTime("$currentYear-$currentMonth-01");
        $end->modify('last day of this month');
        $interval = new DateInterval('P1D');
        $month = new DatePeriod($start, $interval, $end);
        $daysArray = [];
        foreach ($month as $date) {
            $daysArray[] = $date->format('d M y');
        }

        return $daysArray;
    }
}