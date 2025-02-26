<?php

namespace App\Doctrine\Type;

use App\Service\Interfaces\CrypticInterface;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use RuntimeException;

class EncryptedStringType extends Type
{

    public const NAME = 'encrypted_string';

    private static ?CrypticInterface $cryptService = null;

    public static function setCryptic(CrypticInterface $crypticService): void
    {
        self::$cryptService = $crypticService;
    }

    /**
     * @throws Exception
     */
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getDoctrineTypeMapping('text');
    }

    public function getName(): string
    {
        return self::NAME;
    }

//Method "Doctrine\DBAL\Types\Type::convertToDatabaseValue()" might add "mixed" as a native return type declaration in the future.
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return null;
        }

        if (!self::$cryptService) {
            throw new RuntimeException('CryptService not set');
        }

        return self::$cryptService->decrypt($value);

    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (is_null($value)) {
            return null;
        }

        if (!self::$cryptService) {
            throw new RuntimeException('CryptService not set');
        }

        return self::$cryptService->encrypt($value);
    }
}