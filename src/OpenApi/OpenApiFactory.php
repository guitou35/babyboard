<?php

namespace App\OpenApi;

use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Schema;
use ApiPlatform\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated)
    {
    }
    
    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        /** @var PathItem $pathItem */
        foreach($openApi->getPaths()->getPaths() as $key => $pathItem) {
           if($pathItem->getGet()?->getSummary()  === 'hidden') {
            $openApi->getPaths()->addPath($key, $pathItem->withGet(null));
           }
        }

        new Schema('Change', 'Change', 'object', 'The change', [
            'id' => [
                'type' => 'string',
                'format' => 'uuid',
                'description' => 'The id of the change',
            ],
            'type' => [
                'type' => 'string',
                'description' => 'The type of the change',
            ],
            'heure' => [
                'type' => 'string',
                'format' => 'time',
                'description' => 'The time of the change',
            ],
            'contenu' => [
                'type' => 'array',
                'items' => [
                    'type' => 'string',
                ],
                'description' => 'The content of the change',
            ],
        ]);

        $openApi->getPaths()->addPath('/token/refresh', 
        new PathItem($summary = 'refresh token', $description = 'refresh token')
    );

        
       
        return $openApi;
    }
}   