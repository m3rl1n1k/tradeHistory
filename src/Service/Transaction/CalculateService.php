<?php

namespace App\Service\Transaction;

use App\Entity\Transaction;
use App\Entity\Wallet;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

class CalculateService implements CalculationInterface
{
    public function __construct()
    {
    }


    /**
     * @param string $flag
     * @param Transaction|null $transaction
     * @param array $options
     * @return void
     */
    public function calculate(string $flag, ?Transaction $transaction = null, array $options = []): void
    {
        match ($flag) {
            'new' => $this->newTransaction($transaction->getWallet(), $transaction),
            'edit' => $this->editTransaction($transaction->getWallet(), $transaction, $options['oldAmount']),
            'remove' => $this->removeTransaction($transaction->getWallet(), $transaction),
            'default' => throw new ParameterNotFoundException("Flag $flag not found!")
        };
    }

    private function newTransaction($wallet, $transaction): void
    {
        /** @var Transaction $transaction */
        /** @var Wallet $wallet */
        if ($transaction->isIncome()) {
            $wallet->setAmount($wallet->increment($transaction->getAmount()));
        }
        if ($transaction->isExpense()) {
            $wallet->setAmount($wallet->decrement($transaction->getAmount()));
        }
    }

    private function editTransaction($wallet, $transaction, $oldAmount): void
    {
        $this->CurrentMoreOldAmount($wallet, $transaction, $oldAmount);
        if ($transaction->getAmount() === $oldAmount && $transaction->isExpense()) {
            $wallet->setAmount($wallet->getAmount());
        }
        if ($transaction->getAmount() === $oldAmount && $transaction->isIncome()) {
            $wallet->setAmount($wallet->getAmount());
        }
    }

    private function CurrentMoreOldAmount(Wallet $wallet, Transaction $transaction, float $oldAmount): void
    {
        if ($transaction->isExpense()) {
            if ($transaction->getAmount() > $oldAmount) {
                $difference = $transaction->getAmount() - $oldAmount;
                $newAmount = $wallet->getAmount() - abs($difference);
                $wallet->setAmount($newAmount);
            } else {
                $amount = $wallet->getAmount() + ($oldAmount - $transaction->getAmount());
                $wallet->setAmount($amount);
            }
        }
        if ($transaction->isIncome()) {
            if ($transaction->getAmount() > $oldAmount) {
                $wallet->setAmount(
                    $wallet->getAmount() + abs($transaction->getAmount() - $oldAmount)
                );
            } else {
                $wallet->setAmount(
                    $wallet->getAmount() - abs($transaction->getAmount() - $oldAmount)
                );
            }
        }
    }

    private function removeTransaction($wallet, $transaction): void
    {
        $amount = $transaction->getAmount();
        if ($transaction->isExpense($transaction)) {
            $amount = $wallet->increment($amount);
        } else {
            $amount = $wallet->decrement($amount);
        }
        $wallet->setAmount($amount);
    }
}