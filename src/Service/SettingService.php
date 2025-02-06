<?php

namespace App\Service;

use App\Entity\UserSetting;
use App\Repository\UserRepository;

class SettingService
{
    public static UserSetting $setting;

    public function __construct(protected UserRepository $userEntity)
    {
        $user = $this->userEntity->getUser();
        if ($user !== null) {
            self::$setting = $user->getSetting();
        }
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

    public static function getColorExpenseChart(): ?string
    {
        return self::$setting->getColorExpenseChart();
    }

    public static function getColorIncomeChart(): ?string
    {
        return self::$setting->getColorIncomeChart();
    }

    public static function isShowColorInTransactionList(): ?bool
    {
        return self::$setting->isShowColorInTransactionList();
    }
}