<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\WalletRepository;
use Quandl;
use Symfony\Bundle\SecurityBundle\Security;

class WalletService
{
    public function __construct(protected WalletRepository $walletRepository,
                                protected Security         $security,
                                protected ExchangeService  $exchangeService)
    {
    }

    public function getTotal(): float
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $sum = 0;
        $wallets = $this->walletRepository->findBy(['user' => $user->getId()]);
        foreach ($wallets as $wallet) {
            if ($wallet->getCurrency() === $user->getCurrency())
                $sum += $wallet->getAmount();
            else
                $sum += $wallet->getAmount() * $this->exchangeService->currencyExchange("{$wallet->getCurrency()}_{$user->getCurrency()}");
        }
        return $sum;
    }

    public function editWallet(Wallet $wallet, string $currency): string
    {
        $number = $wallet->getNumber();
        return $currency . substr($number, 3);
    }
}