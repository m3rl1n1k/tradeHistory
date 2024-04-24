<?php

namespace App\Service;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserService
{
public function __construct(
    protected Security $security
)
{
}

public function getUser():UserInterface
{
    return $this->security->getUser();
}
}