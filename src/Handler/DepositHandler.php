<?php

namespace App\Handler;

use App\Services\DepositService;

class DepositHandler
{
    public function __construct(
        protected DepositService $depositService
    )
    {
    }

    public function handler(): void
    {
        $this->depositService->depositStatusChecker();
    }
}