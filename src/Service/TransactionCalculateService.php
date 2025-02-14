<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\Wallet;
use App\Service\Interfaces\TransactionCalculationInterface;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

class TransactionCalculateService implements TransactionCalculationInterface
{


    /**
     * @param string $flag
     * @param object|null $object
     * @param array $options
     * @return void
     */
    public function calculate(string $flag, ?object $object = null, array $options = []): void
    {
        /** @var Transaction $object */
        match ($flag) {
            'new' => $this->newTransaction($object->getWallet(), $object),
            'edit' => $this->editTransaction($object->getWallet(), $object, $options['oldAmount']),
            'remove' => $this->removeTransaction($object->getWallet(), $object),
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