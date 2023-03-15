<?php

namespace App\Entity\Enum;

enum Status: string
{
    case Draft =  'brouillon';
    case Published = 'en ligne';
    case Archived = 'archive';

    // public function getColor(?self $value): ?string
    // {
    //     return match ($value) {
    //         // self::Draft() => 'secondary',
    //         // self::Published() => 'success',
    //         // self::Archived() => 'danger',

    //         'brouillon' => 'secondary',
    //         'en ligne' => 'success',
    //         'archive' => 'danger',
    //     };
    // }

    public function getColor(): ?string
    {
        $value = $this->value;
        return match ($value) {
            'brouillon' => 'secondary',
            'en ligne' => 'success',
            'archive' => 'danger',
        };
    }
}
