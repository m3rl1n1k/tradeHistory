<?php

namespace App\Services;


use App\Enum\DepositSettingsEnum;
use App\Repository\DepositRepository;


class DepositService
{
    public function __construct(
        protected DepositRepository $depositRepository,
    )
    {
    }

    public function depositStatusChecker(): void
    {
        $deposits = $this->depositRepository->findAll();
        foreach ($deposits as $deposit) {
            if ($deposit->getStatus() == DepositSettingsEnum::CLOSE) {
                $deposit->setStatus($deposit->setOpen());
                $this->depositRepository->save($deposit);
            }
        }
    }

    public function calculatePercent(): int
    {
        $res = 0;
        $this->depositRepository->
        return $res;
    }

    protected function prepare($data, bool $encode = false): string
    {
        $res = "No data";
        if ($encode) {
            $res = json_encode($data);
        }
        if (!$encode) {
            $res = json_decode($data);
        }
        return $res;
    }

}

