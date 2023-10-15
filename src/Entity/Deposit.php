<?php

namespace App\Entity;

use App\Enum\DepositSettingsEnum;
use App\Repository\DepositRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use function PHPUnit\Framework\isNull;

#[ORM\Entity(repositoryClass: DepositRepository::class)]
class Deposit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $amount = null;

    #[ORM\Column]
    private ?int $percent = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_open = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_close = null;

    #[ORM\Column(length: 25, options: [
        'default' => DepositSettingsEnum::OPEN
    ])]
    private ?string $status = null;

    #[ORM\Column]
    private ?float $startAmount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getPercent(): ?int
    {
        return $this->percent;
    }

    public function setPercent(int $percent): static
    {
        $this->percent = $percent;

        return $this;
    }

    public function getDateOpen(): ?\DateTimeInterface
    {
        return $this->date_open;
    }

    public function setDateOpen(\DateTimeInterface $date_open): static
    {
        $this->date_open = $date_open;

        return $this;
    }

    public function getDateClose(): ?\DateTimeInterface
    {
        return $this->date_close;
    }

    public function setDateClose(\DateTimeInterface $date_close): static
    {
        $this->date_close = $date_close;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function setActive(): int
    {
        return DepositSettingsEnum::ACTIVE;
    }

    public function setNotActive(): int
    {
        return DepositSettingsEnum::NOT_ACTIVE;
    }

    public function setClose(): int
    {
        return DepositSettingsEnum::CLOSE;
    }

    public function setOpen(): int
    {
        return DepositSettingsEnum::OPEN;
    }

    public function getStartAmount(): ?float
    {
        return $this->startAmount;
    }

    public function setStartAmount(float $startAmount): static
    {
        $this->startAmount = $startAmount;

        return $this;
    }
}
