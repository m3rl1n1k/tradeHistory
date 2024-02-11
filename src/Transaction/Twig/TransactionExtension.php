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
		];
	}
	
	public function numberToName(int $type): string
	{
		$types = [
			TransactionEnum::INCOME => "Income",
			TransactionEnum::EXPENSE => "Expense",
			TransactionEnum::TRANSACTION => "Transaction",
		];
//		if ($colorful){
//
//		}
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
}