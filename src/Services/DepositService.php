<?php

namespace App\Services;

use App\Entity\Deposit;
use App\Enum\DepositSettingsEnum;
use App\Repository\DepositRepository;
use DateTime;


class DepositService
{
    public function __construct(
        protected DepositRepository $depositRepository,
    )
    {
    }

    public function calculateDeposit(): array
    {
        $depositsList = [];
        $deposits = $this->depositRepository->findAll();
        foreach ($deposits as $deposit) {
            $status = $this->checkStatus($deposit->getStatus(), DepositSettingsEnum::ACTIVE);
            $date = $deposit->getDateClose() > new DateTime();
            if ($status && $date) {
                $deposit->setPercent($this->calculatePercent($deposit));
                $depositsList[] = $deposit;
            }
        }
        return $depositsList;
    }

    protected function calculatePercent(Deposit $deposit): int
    {
        $percent = $deposit->getPercent();
        $calculate = $deposit->getStartAmount()*$percent;
        //term 30 days
        //100% / 30 days
        //every day add 1% from all amount
        //
        $res = 0;

        return $res;
    }

    protected function checkStatus($list, $needle): ?bool
    {
        $needle = array($needle);
        $array = json_decode($list, JSON_OBJECT_AS_ARRAY);
        return in_array($array, $needle);
    }
}

