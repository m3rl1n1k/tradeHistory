<?php

namespace App\Service;

use App\Entity\UserSetting;
use App\Repository\UserRepository;

class SettingService
{
    public static UserSetting $setting;

    public function __construct(protected UserRepository $userEntity)
    {
        self::$setting = $this->userEntity->getUser()->getSetting();
    }

    public static function isColoredCategories(): bool
    {
        return self::$setting->isColoredCategories();
    }

    public static function isColoredParentCategories(): bool
    {
        return self::$setting->isColoredParentCategories();
    }

    public static function getDefaultColorForCategoryAndParent(): string
    {
        return self::$setting->getDefaultColorForCategoryAndParent();
    }

    public static function getTransactionsPerPage(): ?int
    {
        return self::$setting->getTransactionsPerPage();
    }
}