<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class Access extends AbstractController
{
	
	public function accessDenied(object $transaction, User $user): void
	{
		if ($user->getEmail() !== $transaction->getUserId()) {
			throw $this->createAccessDeniedException('You don\'t have access! ');
		}
	}
	
}