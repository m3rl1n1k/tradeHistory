<?php

namespace App\Controller;

use Exception;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

abstract class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    use TargetPathTrait;

    /**
     * @throws Exception
     */

}