<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\Transfer;
use App\Entity\Wallet;
use App\Enum\TransactionTypeEnum;
use App\Service\Interfaces\CurrencyConverterInterface;
use App\Service\Interfaces\TransferCalculationInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TransferCalculateService implements TransferCalculationInterface
{
    public function __construct(protected EntityManagerInterface $entityManager, protected CurrencyConverterInterface $currencyConverter)
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
            'default' => throw new NotFoundHttpException('Flag not found')
        };

    }

    private function newTransfer(object $walletOut, object $walletIn, float $amount): void
    {
//        try {
//            $this->entityManager->beginTransaction();
        //from out minus amount in plus amount and check if currency same
        /** @var Wallet $walletOut */
        $sum = $walletOut->decrement($amount);
        $walletOut->setAmount($sum);
        $amount = $this->currencyConverter->convert($amount, $walletOut->getCurrency(), $walletIn->getCurrency());
        $sum = $walletIn->increment($amount);
        $walletIn->setAmount($sum);
        $this->createTransaction($amount, $walletOut, $walletIn, ['rate' => $this->currencyConverter->getRate()]);
//            $this->entityManager->commit();
//        } catch (Exception $e) {
//            $this->entityManager->rollback();
//        }
    }

    private function createTransaction($amount, $walletOut, $walletIn, array $options = []): void
    {
        $transaction = new Transaction();
        $transaction->setUser($walletOut->getUser());
        $transaction->setWallet($walletIn);
        $transaction->setDate(new DateTime());
        $transaction->setAmount($amount);
        $out = $walletOut->getname() ?? $walletOut->getNumber();
        $in = $walletIn->getName() ?? $walletIn->getNumber();
        $message = "Transfer from $out to $in. ";

        if (!empty($options['rate'])) {
            $rate = $options['rate'];
            $message .= "Using rate $rate. ";
        }

        $transaction->setDescription($message);
        $transaction->setType(TransactionTypeEnum::Transfer->value);
        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }
}