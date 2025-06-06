<?php 
namespace App\ApiResource\OpenApi;
// src/OpenApi/OpenApiFactory.php


use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Model\SecurityScheme;

class OpenApiFactory implements OpenApiFactoryInterface
{
    public function __construct(private OpenApiFactoryInterface $decorated) {}

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        $components = $openApi->getComponents();
        $securitySchemes = $components->getSecuritySchemes() ?? [];

        $securitySchemes['bearerAuth'] = new SecurityScheme(
             type: 'http',
                description: 'JWT using Bearer scheme',
                scheme: 'bearer',
                bearerFormat: 'JWT'
        );

        $components = $components->withSecuritySchemes($securitySchemes);

        return $openApi->withComponents($components);
        // Ne pas ajouter .withSecurity() ici => ça ne s'applique pas globalement
    }
}
