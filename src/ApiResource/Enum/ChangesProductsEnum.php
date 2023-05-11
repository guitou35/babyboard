<?php

namespace App\ApiResource\Enum;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\ApiResource\Enum\EnumApiResourceTrait;

#[ApiResource(normalizationContext: ['groups' => ['read']]),
    GetCollection(provider: ChangesProductsEnum::class.'::getCases'),
    Get(provider: ChangesProductsEnum::class.'::getCase')
]
enum ChangesProductsEnum : String
{
    case LINIMENT = "Liniment";
    case TALC = "Talc";
    case LINGETTES = "Lingettes";

    public static function values(): array
    {
        return array_column(self::cases(), 'name');
    }
    use EnumApiResourceTrait;
}