<?php

namespace App\Trait;

use Symfony\Component\Security\Core\User\UserInterface;

trait AccessTrait
{
    public function accessDenied(int $object, ?UserInterface $user): void
    {
        if ($user->getId() !== $object) {
            throw $this->createAccessDeniedException('You don\'t have access! ');
        }
    }
}