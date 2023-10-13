<?php

namespace App\Services;


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
            $deposit->setStatus($deposit->setClose());
        }
    }

}

