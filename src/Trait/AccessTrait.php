<?php

namespace App\Trait;

use Symfony\Component\Security\Core\User\UserInterface;

trait AccessTrait
{

    public function accessDenied($object, ?UserInterface $user): void
    {
        if ($user->getId() != $object->getUserId()) {
            throw $this->createAccessDeniedException('You don\'t have access! ');
        }
    }

}