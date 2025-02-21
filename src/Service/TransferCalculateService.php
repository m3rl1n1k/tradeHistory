<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\Transfer;
use App\Entity\Wallet;
use App\Enum\TransactionTypeEnum;
use App\Service\Interfaces\TransferCalculationInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransferCalculateService implements TransferCalculationInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
    }

    public function calculate(string $flag, ?object $object = null, array $options = []): void
    {

        /** @var Transfer $object */
        $walletOut = $object->getWalletOut();
        $walletIn = $object->getWalletIn();
        $amount = $object->getAmount();

        if ($walletIn->getNumber() === $walletOut->getNumber()) {
            throw new Exception("Can't make transfer to same wallet");
        }
//        if ($walletOut->getAmount() > $amount) {
//            throw new Exception("Amount must be less than or equal to " . $walletOut->getAmount());
//        }
        match ($flag) {
            'new' => $this->newTransfer($walletOut, $walletIn, $amount),
//            'edit' => $this->editTransfer($walletOut, $walletIn, $amount, $options['oldAmount']),
            'default' => throw new NotFoundHttpException('Flag not found')
        };

    }

    private function newTransfer(object $walletOut, object $walletIn, float $amount): void
    {
        $this->entityManager->beginTransaction();
        //from out minus amount in plus amount and check if currency same
        /** @var Wallet $walletOut */
        $sum = $walletOut->decrement($amount);
        $walletOut->setAmount($sum);
        $sum = $walletIn->increment($amount);
        $walletIn->setAmount($sum);
        $this->createTransaction($amount, $walletOut, $walletIn);
        $this->entityManager->commit();
    }

    private function createTransaction($amount, $walletOut, $walletIn): void
    {
        $transaction = new Transaction();
        $transaction->setUser($walletIn->getUser());
        $transaction->setWallet($walletIn);
        $transaction->setDate(new DateTime());
        $transaction->setAmount($amount);
        $out = $walletOut->getname() ?? $walletOut->getNumber();
        $in = $walletIn->getName() ?? $walletIn->getNumber();
        $transaction->setDescription("Transfer from $out to $in");
        $transaction->setType(TransactionTypeEnum::Transfer->value);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }

    private function editTransfer(object $walletOut, object $walletIn, float $amount, float $oldAmount): void
    {
        $amount = abs($amount - $oldAmount);
        $walletOut->decrement($amount);
        $walletIn->increment($amount);
    }
}