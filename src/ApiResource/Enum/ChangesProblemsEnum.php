<?php

namespace App\ApiResource\Enum;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\ApiResource\Enum\EnumApiResourceTrait;

#[ApiResource(normalizationContext: ['groups' => ['read']]),
    GetCollection(provider: ChangesProblemsEnum::class.'::getCases'),
    Get(provider: ChangesProblemsEnum::class.'::getCase')
]
enum ChangesProblemsEnum : string
{
    case IRRITATION = "Irritation";
    case BUTTON = "Boutons";

    public static function values(): array
    {
        return array_column(self::cases(), 'name');
    }

    use EnumApiResourceTrait;
}