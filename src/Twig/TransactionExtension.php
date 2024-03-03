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
			new TwigFunction('type', [$this, 'numberToName']),
			new TwigFunction('color', [$this, 'color']),
			new TwigFunction('type_transaction', [$this, 'typeTransaction']),
		];
	}
	
	public function numberToName(int $type): string
	{
		$types = [
			TransactionEnum::Income->value => "Income",
			TransactionEnum::Expense->value => "Expense",
			TransactionEnum::Transaction->value => "Expense",
		];
		return $types[$type];
	}
	
	public function color(int $type): string
	{
		return match ($type) {
			TransactionEnum::Income->value => "btn-outline-success",
			TransactionEnum::Expense->value => "btn-outline-danger",
			TransactionEnum::Transaction->value => "btn-outline-warning",
			default => "btn-outline-info"
		};
	}
	
	public function typeTransaction(int $type): string
	{
		return match ($type) {
			TransactionEnum::Income->value => "Income",
			TransactionEnum::Expense->value => "Expense",
			TransactionEnum::Transaction->value => "Transfer",
			default => ""
		};
	}
}