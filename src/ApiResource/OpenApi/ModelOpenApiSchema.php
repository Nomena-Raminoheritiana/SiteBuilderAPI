<?php
// src/ApiResource/OpenApi/OpenApiSchemas.php

namespace App\ApiResource\OpenApi;

class ModelOpenApiSchema
{
     public const URL_RESOLVER =  [
            'summary' => 'Resolve URL to model id',
            'requestBody' => [
                'content' => [
                    'application/json' => [
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'url' => ['type' => 'string', 'example' => '/some-url'],
                                'modelId' => ['oneOf' => [['type' => 'string'], ['type' => 'integer']]],
                            ],
                            'required' => ['url', 'modelId'],
                        ],
                    ],
                ],
            ],
            'responses' => [
                '200' => [
                    'description' => 'Successful response',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'id' => ['type' => 'integer', 'example' => 1],
                                    'url' => ['type' => 'string', 'example' => '/some-url'],
                                ],
                            ],
                        ],
                    ],
                ],
                '404' => [
                    'description' => 'Not found',
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'error' => ['type' => 'string', 'example' => 'URL not found'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
}
