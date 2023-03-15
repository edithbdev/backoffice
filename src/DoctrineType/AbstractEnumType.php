<?php

namespace App\DoctrineType;

use BackedEnum;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

abstract class AbstractEnumType extends Type
{
    public const NAME = 'status';

    abstract public static function getEnumsClass(): string;

    // public function getName(): string
    // {
    //     throw new \Exception('getName should be overwritten by child class');
    // }


    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'TEXT';
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string|int|null
    {
        if ($value instanceof \BackedEnum) {
            return $value->value;
        }
        return null;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?BackedEnum
    {
        if (false === enum_exists($this->getEnumsClass(), true)) {
            throw new \LogicException("This class should be an enum");
        }
        // 🔥 https://www.php.net/manual/en/backedenum.tryfrom.php
        return $this::getEnumsClass()::tryFrom($value);
    }
}
