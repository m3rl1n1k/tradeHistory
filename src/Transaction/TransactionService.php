<?php

namespace App\Transaction;

use App\Entity\Transaction;
use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\TransactionRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

class TransactionService implements TransactionServiceInterface
{
    /**
     * @var Transaction[]
     */
    private array $transactions;

    public function __construct(protected UserRepository        $userRepository,
                                protected TransactionRepository $transactionRepository,
    )
    {
        $this->transactions = $this->transactionRepository->getAll();
    }

    public function getTransactionsPerPeriod($dateStart, $dateEnd): array
    {
        return $this->transactionRepository->getTransactionsPerPeriod($dateStart, $dateEnd);
    }

    public function getTransactions(bool $is_array = false): array|Query
    {
        if ($is_array) {
            return $this->transactions;
        }
        return $this->transactionRepository->getUserTransactionsQuery();
    }

    public function calculate(Wallet $wallet, Transaction $transaction, float $oldAmount = 0, bool $newTransaction =
    false):
    void
    {
        if ($newTransaction) {
            if ($transaction->isIncome()) {
                $wallet->setAmount($wallet->increment($transaction->getAmount()));
            }
            if ($transaction->isExpense()) {
                $wallet->setAmount($wallet->decrement($transaction->getAmount()));
            }
        } else {
            $this->CurrentMoreOldAmount($wallet, $transaction, $oldAmount);
            if ($transaction->getAmount() === $oldAmount && $transaction->isExpense()) {
                $wallet->setAmount($wallet->getAmount());
            }
            if ($transaction->getAmount() === $oldAmount && $transaction->isIncome()) {
                $wallet->setAmount($wallet->getAmount());
            }
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

    public function removeTransaction(Wallet $wallet, Transaction $transaction): void
    {
        $amount = $transaction->getAmount();
        if ($transaction->getType() === TransactionEnum::Expense->value) {
            $amount = $wallet->increment($amount);
        } else {
            $amount = $wallet->decrement($amount);
        }
        $wallet->setAmount($amount);
    }

    public function getSum(float|array|int|string $transactions, int $type): float
    {
        $sum = 0;
        foreach ($transactions as $transaction) {
            if ($transaction->getType() === $type) {
                $sum += $transaction->getAmount();
            }
        }
        return $sum;
    }

    public function groupTransactionsByCategory(array $transactions): array
    {
        $groupedTransactions = [];

        foreach ($transactions as $transaction) {
            $category = $transaction->getSubCategory();
            if (!$transaction->getSubCategory()) {
                $groupedTransactions['No category'][] = $transaction;
            } else {
                $groupedTransactions[$category->getName()][] = $transaction;
            }
        }

        return $groupedTransactions;
    }

    public function newTransaction(EntityManagerInterface $em, Wallet $wallet, int $amount, User $user, array $options = []): void
    {
        $msg = 'Transfer from %s';
        $msg = sprintf($msg, $wallet->getName() ?? $wallet->getNumber());

        if (isset($options['rate'])) {
            $msg = 'Transfer from %s with exchange rate: %s';
            $msg = sprintf($msg, $wallet->getNumber(), $options['rate']);
        }
        $date = new DateTime('now');

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setWallet($wallet)
            ->setDate($date)
            ->setType(TransactionEnum::Transfer->value)
            ->setDescription($msg)
            ->setUserId($user);

        $em->persist($transaction);
    }
}