<?php

namespace App\ApiResource\Enum;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\ApiResource\Enum\EnumApiResourceTrait;

#[ApiResource(normalizationContext: ['groups' => ['read']]),
    GetCollection(provider: ChangesTypeEnum::class.'::getCases'),
    Get(provider: ChangesTypeEnum::class.'::getCase')
]
enum ChangesTypeEnum : String
{
    case COUCHE = "Couche";
    case PETITPOT = "Petit pot";
    case TOILETTE = "Toilette";

    public static function values(): array
    {
        return array_column(self::cases(), 'name');
    }

    use EnumApiResourceTrait;
}