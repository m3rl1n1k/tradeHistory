<?php

namespace App\Twig;

use App\Enum\TransactionTypeEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TransactionExtension extends AbstractExtension
{

    public function getFunctions(): array
    {
        return [
            new TwigFunction('type', [$this, 'type']),
            new TwigFunction('color', [$this, 'color']),
        ];
    }

    public function color(int $type): string
    {
        return match ($type) {
            TransactionTypeEnum::Profit->value => 'style="background-color:rgba(52,211,153)"',
            TransactionTypeEnum::Expense->value => 'style="background-color:rgba(248,113,13)"',
            TransactionTypeEnum::Transfer->value => 'style="background-color:rgba(96,165,250)"',
            default => 'style="background-color:rgba(156,163,175)"',
        };
    }

    public function type(int $type): string
    {
        return match ($type) {
            TransactionTypeEnum::Profit->value => "Income",
            TransactionTypeEnum::Expense->value => "Expense",
            TransactionTypeEnum::Transfer->value => "Transfer"
        };
    }
}