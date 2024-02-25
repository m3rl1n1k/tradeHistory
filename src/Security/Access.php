<?php

namespace App\Security;

use App\Entity\User;
use App\Transaction\Entity\Transaction;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Access extends AbstractController
{
	
	public function accessDenied(object $transaction, User $user): void
	{
		if ($user->getUserIdentifier() !== $transaction->getUserId()) {
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