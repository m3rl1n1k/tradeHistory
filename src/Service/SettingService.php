<?php

namespace App\Service;

use App\Repository\UserRepository;
use stdClass;

class SettingService
{
    public static mixed $user;

    public function __construct(protected UserRepository $userEntity)
    {

        self::setUser($this->userEntity->getUser());
    }

    public static function setUser(mixed $user): void
    {
        self::$user = $user;
    }

    public static function getSettings(): ?array
    {
        return self::$user->getSetting();
//        return json_decode(self::$user->settings, true);
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