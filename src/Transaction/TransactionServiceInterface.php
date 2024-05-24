<?php

namespace App\Transaction;

use Doctrine\ORM\Query;

interface TransactionServiceInterface
{
    public function getTransactions(): Query;

    public function getTransactionsPerPeriod($dateStart, $dateEnd): array;

}