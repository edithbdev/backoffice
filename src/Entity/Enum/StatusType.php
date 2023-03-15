<?php

namespace App\Entity\Enum;

use App\Entity\Enum\Status;
use App\DoctrineType\AbstractEnumType;

class StatusType extends AbstractEnumType
{
    public const NAME = 'status';

    public function getName(): string // le nom du type
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string // la classe qui contient les constantes
    {
        return Status::class;
    }
}
