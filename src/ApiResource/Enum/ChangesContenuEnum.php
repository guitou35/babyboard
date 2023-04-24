<?php

namespace App\ApiResource\Enum;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\ApiResource\Enum\EnumApiResourceTrait;

#[ApiResource(normalizationContext: ['groups' => ['read']]),
    GetCollection(provider: ChangesContenuEnum::class.'::getCases'),
    Get(provider: ChangesContenuEnum::class.'::getCase')
]
enum ChangesContenuEnum : String
{
    case SELLES = "Selles";
    case URINE = "Urines";

    public static function values(): array
    {
        return array_column(self::cases(), 'name');
    }

    use EnumApiResourceTrait;

}