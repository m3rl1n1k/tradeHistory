<?php

namespace App\Enum;

use App\Trait\EnumToArrayTrait;

enum CategoryEnum: int
{
	use EnumToArrayTrait;
	
	case Main = 1;
	case Sub = 2;
	
}
