<?php

namespace App\OpenApi;

Use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\Model\RequestBody;
Use ApiPlatform\Core\OpenApi\OpenApi;

class OpenApiFactory implements OpenApiFactoryInterface{

    public function __construct(private OpenApiFactoryInterface $decorated) {
    }

    public function __invoke(array $context = []): OpenApi{

        $openApi = $this->decorated->__invoke ($context);
        /** @var PathItem $path */
        foreach ($openApi->getPaths()->getPaths() as $key => $path){
            if ($path->getGet() && $path->getGet()->getSummary() === 'hidden'){
                $openApi->getPaths()->addPath($key, $path->withGet(nuLl));
            }
        }

        $schemas = $openApi->getComponents()->getSecuritySchemes();
        $schemas['bearerAuth'] = new \ArrayObject([
            'type' => 'http',
            'scheme' => 'bearer',
            'bearerFormat' => 'JWT'
        ]);
        $openApi = $openApi->withSecurity([['bearerAuth' => []]]);

        $schemas = $openApi->getComponents()->getSchemas();
        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);
        $schemas['RefreshToken'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'string',
                ],
            ],
        ]);
        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'john@doe.fr',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'azerty',
                ]
            ]
        ]);
        $schemas['Register'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'email' => [
                    'type' => 'string',
                    'example' => 'john@doe.fr',
                ],
                'firstname' => [
                    'type' => 'string',
                    'example' => 'john',
                ],
                'lastname' => [
                    'type' => 'string',
                    'example' => 'doe',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'azerty',
                ]
            ]
        ]);

        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiLogin',
                tags: ['Auth'],
                responses: [
                    '200' => [
                        'description' => 'Get JWT token and refresh token',
                        'content'=> [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token'
                                ]
                            ]
                        ]
                    ]
                ],
                summary: 'Get JWT token to login.',
                requestBody: new RequestBody(
                    description: 'Generate new JWT Token',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials'
                            ]
                        ]
                    ])
                ),
            )
        );
        $openApi->getPaths()->addPath('/api/login_check', $pathItem);

        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiRefreshToken',
                tags: ['Auth'],
                responses: [
                    '200' => [
                        'description' => 'Get JWT token and refresh token',
                        'content'=> [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token'
                                ]
                            ]
                        ]
                    ]
                ],
                summary: 'Regenerate JWT token to login.',
                requestBody: new RequestBody(
                    description: 'Regenerate new JWT Token',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/RefreshToken'
                            ]
                        ]
                    ])
                ),
            )
        );
        $openApi->getPaths()->addPath('/api/token_refresh', $pathItem);

        $pathItem = new PathItem(
            post: new Operation(
                operationId: 'postApiRegister',
                tags: ['Auth'],
                responses: [
                    '201' => [
                        'description' => 'Get new User (Artist)',
                        'content'=> [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/User'
                                ]
                            ]
                        ]
                    ]
                ],
                summary: 'Artist registration.',
                requestBody: new RequestBody(
                    description: 'Create new User (Artist)',
                    content: new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Register'
                            ]
                        ]
                    ])
                ),
            )
        );
        $openApi->getPaths()->addPath('/api/register', $pathItem);

        return $openApi;
    }

}

