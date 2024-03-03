<?php

namespace App\Exception;

use Exception;
use Throwable;

class CurrencyNotFoundException extends Exception
{
	public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
	{
		parent::__construct($message, $code, $previous);
	}
}