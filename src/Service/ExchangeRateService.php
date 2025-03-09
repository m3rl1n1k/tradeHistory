<?php

namespace App\Service;

use App\Entity\ExchangeRate;
use Doctrine\ORM\EntityManagerInterface;
use FreeCurrencyApi\FreeCurrencyApi\FreeCurrencyApiClient;
use FreeCurrencyApi\FreeCurrencyApi\FreeCurrencyApiException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

readonly class ExchangeRateService
{
    public function __construct(
        private CacheInterface         $cache,
        private EntityManagerInterface $em
    )
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function getExchangeRate(string $currencyIn, string $currencyFrom)
    {
        return $this->cache->get('exchange_rate_' . $currencyIn, function (ItemInterface $item) use ($currencyIn, $currencyFrom) {
            $item->expiresAfter(86400); // 24 години

            /** @var ExchangeRate $rate */
            $rate = $this->em->getRepository(ExchangeRate::class)
                ->findLatestRate($currencyIn);
            if (!$rate) {
                $rate = $this->fetchFromApi($currencyIn); // Запит до API
                $this->saveToDatabase($rate, $currencyIn);
            }

            return $rate->getCurrencyRate();
        });

    }

    /**
     * @throws FreeCurrencyApiException
     */
    private function fetchFromApi(string $currencyIn): array
    {
        $freecurrencyapi = new FreeCurrencyApiClient('fca_live_4iO7B7x9vF96y7zItKi4mPyMMNMMVXrX0pCf9JUG');
        $currency = $freecurrencyapi->latest($this->getPair($currencyIn));

        return $currency['data'];
    }

    private function getPair(string $currencyIn): array
    {
        return match ($currencyIn) {
            'PLN' => [
                'currencies' => ['EUR', 'USD'],
                'base_currency' => 'PLN',
            ],
            'USD' => [
                'currencies' => ['PLN', 'EUR'],
                'base_currency' => 'USD',
            ],
            'EUR' => [
                'currencies' => ['PLN', 'USD'],
                'base_currency' => 'EUR',
            ],
//            'UAH' => [
//                'currencies' => 'PLN,EUR,USD',
//                'base_currency' => 'UAH',
//            ],
        };
    }

    private function saveToDatabase(array $currency, $user_currency): void
    {
        $exchangeRate = new ExchangeRate();
        $exchangeRate->setCurrencyRate($currency);
        $exchangeRate->setUserCurrency($user_currency);
        $exchangeRate->setDate();
        $this->em->persist($exchangeRate);
        $this->em->flush();
    }
}