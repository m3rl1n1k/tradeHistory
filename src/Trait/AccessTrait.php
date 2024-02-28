<?php

namespace App\Trait;

use App\Entity\User;

trait AccessTrait
{
	public function accessDenied(object $object, User $user): void
	{
		if ($user->getUserIdentifier() !== $object->getUserId()) {
			throw $this->createAccessDeniedException('You don\'t have access! ');
		}
	}
	
	public function accessCustom($condition): void
	{
		if ($condition){
			throw $this->createAccessDeniedException('You don\'t have access! ');
		}
	}
	
}