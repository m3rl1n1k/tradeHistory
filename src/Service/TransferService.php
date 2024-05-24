<?php

namespace App\Service;

use App\Entity\Transfer;
use App\Entity\Wallet;
use App\Transaction\TransactionService;
use LogicException;

class TransferService
{
    public function __construct(
        protected CurrencyConverterService $converterService,
        protected TransactionService       $transactionService
    )
    {
    }

    public function calculate(Transfer $transfer, $user): void
    {
        $outWallet = $transfer->getFromWallet();
        $inWallet = $transfer->getToWallet();

        $outCurrency = $outWallet->getCurrency();
        $inCurrency = $inWallet->getCurrency();

        $this->checker($outWallet, $inWallet, $transfer);

        if ($outCurrency !== $inCurrency) {
            $convertedAmount = $this->converterService->convertAmount(
                $outCurrency,
                $inCurrency,
                $transfer->getAmount()
            );
            $outWallet->setAmount($outWallet->decrement($transfer->getAmount()));
            $inWallet->setAmount($inWallet->increment($convertedAmount));

            $rate = [
                'rate' => $this->converterService->getRate($outCurrency, $inCurrency)
            ];

            $this->transactionService->createTransaction($inWallet, $convertedAmount, $user, $rate);
        } else {
            $outWallet->setAmount($outWallet->decrement($transfer->getAmount()));
            $inWallet->setAmount($inWallet->increment($transfer->getAmount()));
            $this->transactionService->createTransaction($inWallet, $transfer->getAmount(), $user);
        }

    }

    private function checker(?Wallet $outWallet, ?Wallet $inWallet, Transfer $transfer): void
    {
        if ($outWallet->getNumber() === $inWallet->getNumber()) {
            throw new LogicException('Can\'t make transfer to same wallet!');
        }
        if ($outWallet->getAmount() === 0) {
            throw new LogicException('Can\'t make transfer from empty wallet!');
        }
        if ($outWallet->getAmount() < $transfer->getAmount()) {
            throw new LogicException('Enter same or less amount, that you have on wallet!');
        }
    }

}