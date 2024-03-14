<?php

namespace App\Twig;

use App\Enum\TransactionEnum;
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
			TransactionEnum::Income->value => "btn-outline-success",
			TransactionEnum::Expense->value => "btn-outline-danger",
			TransactionEnum::Transfer->value => "btn-outline-warning",
			default => "btn-outline-info"
		};
	}
	
	public function type(int $type): string
	{
		return match ($type) {
			TransactionEnum::Income->value => "Income",
			TransactionEnum::Expense->value => "Expense",
			TransactionEnum::Transfer->value => "Transfer",
			default => ""
		};
	}
}