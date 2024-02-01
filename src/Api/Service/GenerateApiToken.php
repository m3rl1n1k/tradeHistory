<?php

namespace App\Api\Service;

abstract class GenerateApiToken
{
	static public function generateApiToken():string
	{
		return md5(time());
	}
}