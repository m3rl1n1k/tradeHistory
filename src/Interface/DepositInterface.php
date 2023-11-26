<?php

namespace App\Interface;

use App\Entity\Deposit;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface DepositInterface
{
    public function calculate(Deposit $deposit):Deposit;
    public function close(Deposit $deposit):Deposit;
    public function create(Request $request):Response;
}