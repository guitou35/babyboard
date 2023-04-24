<?php


namespace App\ApiResource\Enum;

use ApiPlatform\Metadata\Operation;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiProperty;

trait EnumApiResourceTrait
{
    #[ApiProperty(types: ['https://schema.org/identifier'])]
    public function getId()
    {
        return $this->name;
    }

    #[Groups('read:item')]
    #[ApiProperty(types: ['https://schema.org/name'])]
    public function getValue()
    {
        return $this->value;
    }

    public static function getCases()
    {
        return self::cases();
    }

    public static function getCase(Operation $operation, array $uriVariables)
    {
        $name = $uriVariables['id'] ?? null;
        return self::tryFrom($name);
    }
}