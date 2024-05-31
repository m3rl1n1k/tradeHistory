<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SettingService
{
    protected static mixed $user;

    public function __construct(protected Security $security)
    {
        $user = $this->security->getUser();

        if ($user === null) {
            throw new NotFoundHttpException('User not found');
        }
        /** @var User $user */
        self::$user = $user;
    }

    public static function getSettings(): ?array
    {
        return self::$user->getSetting();
    }

    public static function isCategoryWithColor(): bool
    {
        return self::$user->getSetting()['coloredCategories'];
    }

    public static function isParentCategoryWithColor(): bool
    {
        return self::$user->getSetting()['coloredParentCategories'];
    }

    public static function getDefaultColorForCategory(): string
    {
        return self::$user->getSetting()['defaultColorForCategoryAndParent'];
    }
}