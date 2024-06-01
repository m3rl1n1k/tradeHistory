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
//        self::setUser($this->plug());
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

    private function plug()
    {
        $user = new stdClass();
        $user->id = 6;
        $user->email = 'emailTestUser@email.com';
        $user->roles = [];
        $user->password = '$2y$13$Fp7wIggTVeaSGnGVqUIpveqSswNqGo7VMEERcXHOCv6ugx2Ccb796';
        $user->currency = 'PLN';
        $user->settings = '{"colorIncomeChart": "#8aff93", "colorExpenseChart": "#ff6b6b", "coloredCategories": false, "transactionsPerPage": 100, "categoriesWithoutColor": true, "coloredParentCategories": false, "defaultColorForCategoryAndParent": "#1c6263"}';
        return $user;
    }
}