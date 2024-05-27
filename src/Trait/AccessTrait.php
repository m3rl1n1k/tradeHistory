<?php

namespace App\Trait;

use Symfony\Component\Security\Core\User\UserInterface;

trait AccessTrait
{

    public function accessDenied($object, ?UserInterface $user): void
    {
//        dd($object, $user->getId());
        if ($user->getId() !== $object) {
            throw $this->createAccessDeniedException('You don\'t have access! ');
        }
    }

}