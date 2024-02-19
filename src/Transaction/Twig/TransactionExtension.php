<?php

namespace App\Transaction\Twig;

use App\Transaction\Enum\TransactionEnum;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use function PHPUnit\Framework\matches;

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
			TransactionEnum::INCOME => "Income",
			TransactionEnum::EXPENSE => "Expense",
			TransactionEnum::TRANSACTION => "Transaction",
		];
		return $types[$type];
	}
	
	public function color(int $type): string
	{
		return match ($type) {
			TransactionEnum::INCOME => "btn-outline-success",
			TransactionEnum::EXPENSE => "btn-outline-danger",
			TransactionEnum::TRANSACTION => "btn-outline-warning",
			default => "btn-outline-info"
		};
	}
	
	public function typeTransaction(int $type): string
	{
		return match ($type) {
			TransactionEnum::INCOME => "income",
			TransactionEnum::EXPENSE => "expense",
			TransactionEnum::TRANSACTION => "transaction",
			default => ""
		};
	}
}