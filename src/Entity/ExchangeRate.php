<?php

namespace App\Entity;

use App\Repository\ExchangeRateRepository;
use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExchangeRateRepository::class)]
class ExchangeRate
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "SEQUENCE")]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;
    #[ORM\Column]
    private array $currency_rate = [];

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?DateTimeInterface $date = null;

    #[ORM\Column(length: 4)]
    private ?string $user_currency = null;

    public function getCurrencyRate(): array
    {
        return $this->currency_rate;
    }

    public function setCurrencyRate(array $currency_rate): static
    {
        $this->currency_rate = $currency_rate;

        return $this;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(): static
    {
        $this->date = new DateTime();

        return $this;
    }

    public function getUserCurrency(): ?string
    {
        return $this->user_currency;
    }

    public function setUserCurrency(string $user_currency): static
    {
        $this->user_currency = $user_currency;

        return $this;
    }
}