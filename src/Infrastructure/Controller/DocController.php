<?php

namespace App\Infrastructure\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function swaggerUi(): Response
    {
        $html = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>E-Commerce API Documentation</title>
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/swagger-ui-dist@3/swagger-ui.css">
    <style>
        html { box-sizing: border-box; overflow: -moz-scrollbars-vertical; overflow-y: scroll; }
        *, *:before, *:after { box-sizing: inherit; }
        body { margin: 0; background: #fafafa; }
        #swagger-ui { margin: 20px; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@3/swagger-ui-bundle.js"></script>
    <script>
        SwaggerUIBundle({
            url: "/api/docs.json",
            dom_id: "#swagger-ui",
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIBundle.presets.standalone
            ],
            layout: "BaseLayout",
            deepLinking: true
        });
    </script>
</body>
</html>
HTML;

        return new Response($html);
    }

    #[Route('/api/docs.json', name: 'api_docs')]
    public function apiDocs(): JsonResponse
    {
        $documentation = [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'E-Commerce API',
                'description' => 'This is an e-commerce API for product management',
                'version' => '1.0.0'
            ],
            'servers' => [
                ['url' => 'http://localhost:8080', 'description' => 'Local server']
            ],
            'paths' => [
                '/api/products' => [
                    'get' => [
                        'summary' => 'Get paginated list of products',
                        'description' => 'Retrieve products with optional name filtering and pagination',
                        'tags' => ['Products'],
                        'parameters' => [
                            [
                                'name' => 'page',
                                'in' => 'query',
                                'description' => 'Page number',
                                'required' => false,
                                'schema' => ['type' => 'integer', 'default' => 1]
                            ],
                            [
                                'name' => 'limit',
                                'in' => 'query',
                                'description' => 'Number of items per page (max 100)',
                                'required' => false,
                                'schema' => ['type' => 'integer', 'default' => 10, 'maximum' => 100]
                            ],
                            [
                                'name' => 'name',
                                'in' => 'query',
                                'description' => 'Filter products by name (partial match)',
                                'required' => false,
                                'schema' => ['type' => 'string']
                            ]
                        ],
                        'responses' => [
                            '200' => [
                                'description' => 'Successful operation',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'data' => [
                                                    'type' => 'array',
                                                    'items' => [
                                                        'type' => 'object',
                                                        'properties' => [
                                                            'id' => ['type' => 'string', 'example' => 'prod_12345'],
                                                            'name' => ['type' => 'string', 'example' => 'iPhone 13'],
                                                            'description' => ['type' => 'string', 'example' => 'Latest smartphone'],
                                                            'price' => ['type' => 'number', 'format' => 'float', 'example' => 999.99],
                                                            'category' => ['type' => 'string', 'example' => 'electronics'],
                                                            'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2024-01-01 12:00:00']
                                                        ]
                                                    ]
                                                ],
                                                'pagination' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        'total' => ['type' => 'integer', 'example' => 100],
                                                        'page' => ['type' => 'integer', 'example' => 1],
                                                        'limit' => ['type' => 'integer', 'example' => 10],
                                                        'pages' => ['type' => 'integer', 'example' => 10]
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'post' => [
                        'summary' => 'Create a new product',
                        'description' => 'Create a new product (Admin only - requires authentication)',
                        'tags' => ['Products'],
                        'security' => [['bearerAuth' => []]],
                        'requestBody' => [
                            'required' => true,
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'object',
                                        'required' => ['name', 'description', 'price', 'category'],
                                        'properties' => [
                                            'name' => ['type' => 'string', 'example' => 'iPhone 13'],
                                            'description' => ['type' => 'string', 'example' => 'Latest smartphone from Apple'],
                                            'price' => ['type' => 'number', 'format' => 'float', 'example' => 999.99],
                                            'category' => ['type' => 'string', 'example' => 'electronics']
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'responses' => [
                            '201' => [
                                'description' => 'Product created successfully',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'id' => ['type' => 'string', 'example' => 'prod_12345'],
                                                'name' => ['type' => 'string', 'example' => 'iPhone 13'],
                                                'description' => ['type' => 'string', 'example' => 'Latest smartphone from Apple'],
                                                'price' => ['type' => 'number', 'format' => 'float', 'example' => 999.99],
                                                'price_with_vat' => [
                                                    'type' => 'object',
                                                    'properties' => [
                                                        '4' => ['type' => 'number', 'format' => 'float', 'example' => 1039.99],
                                                        '10' => ['type' => 'number', 'format' => 'float', 'example' => 1099.99],
                                                        '21' => ['type' => 'number', 'format' => 'float', 'example' => 1209.99]
                                                    ]
                                                ],
                                                'category' => ['type' => 'string', 'example' => 'electronics'],
                                                'created_at' => ['type' => 'string', 'format' => 'date-time', 'example' => '2024-01-01 12:00:00']
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            '400' => [
                                'description' => 'Bad request - validation error',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'error' => ['type' => 'string', 'example' => 'Product name cannot be empty']
                                            ]
                                        ]
                                    ]
                                ]
                            ],
                            '401' => [
                                'description' => 'Unauthorized - invalid or missing token',
                                'content' => [
                                    'application/json' => [
                                        'schema' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'error' => ['type' => 'string', 'example' => 'Unauthorized'],
                                                'message' => ['type' => 'string', 'example' => 'Invalid API token']
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type' => 'http',
                        'scheme' => 'bearer',
                        'bearerFormat' => 'JWT'
                    ]
                ]
            ],
            'security' => [
                ['bearerAuth' => []]
            ]
        ];

        return new JsonResponse($documentation);
    }

    #[Route('/api', name: 'api_root')]
    public function apiRoot(): JsonResponse
    {
        return new JsonResponse([
            'message' => 'E-Commerce API',
            'version' => '1.0.0',
            'endpoints' => [
                'documentation' => '/ (Swagger UI)',
                'openapi_spec' => '/api/docs.json',
                'products_list' => '/api/products (GET)',
                'product_create' => '/api/products (POST) - Requires authentication'
            ],
            'authentication' => [
                'type' => 'Bearer Token',
                'header' => 'Authorization: Bearer admintoken'
            ]
        ]);
    }
}
