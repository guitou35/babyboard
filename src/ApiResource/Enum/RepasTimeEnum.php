<?php

namespace App\ApiResource\Enum;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\ApiResource\Enum\EnumApiResourceTrait;

#[ApiResource(
    types: ['https://schema.org/Enumeration'],
    normalizationContext: ['groups' => ['read:item']],
    description: "Repas Time enum used for a repas "
),
    GetCollection(provider: RepasTimeEnum::class.'::getCases'),
    Get(provider: RepasTimeEnum::class.'::getCase')
]
enum RepasTimeEnum : String
{
    case PETITDEJ = "Petit déjeuner";
    case DEJ = "Déjeuner";
    case GOUTER = "Gouter";
    case DINER = "Diner";

    public static function values(): array
    {
        return array_column(self::cases(), 'name');
    }

    use EnumApiResourceTrait;
}