<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\CreateProductUseCase;
use App\Application\UseCase\ListProductsUseCase;
use App\Application\DTO\CreateProductDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use OpenApi\Attributes as OA;

#[Route('/api/products')]
#[OA\Tag(name: 'Products')]
class ProductController
{
    public function __construct(
        private CreateProductUseCase $createProductUseCase,
        private ListProductsUseCase $listProductsUseCase
    ) {}

    #[Route('', name: 'products_list', methods: ['GET'])]
    #[OA\Get(
        path: '/api/products',
        summary: 'List all products',
        parameters: [
            new OA\QueryParameter(name: 'page', description: 'Page number', schema: new OA\Schema(type: 'integer', default: 1)),
            new OA\QueryParameter(name: 'limit', description: 'Items per page', schema: new OA\Schema(type: 'integer', default: 10)),
            new OA\QueryParameter(name: 'name', description: 'Filter by product name', schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'List of products',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Product')),
                        new OA\Property(property: 'pagination', type: 'object')
                    ],
                    type: 'object'
                )
            )
        ]
    )]
    public function list(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, min(100, (int) $request->query->get('limit', 10)));
        $name = $request->query->get('name');

        $filters = $name ? ['name' => $name] : [];

        $result = $this->listProductsUseCase->execute($filters, $page, $limit);

        $data = [
            'data' => array_map(function ($product) {
                return [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'price_without_vat' => $product->getPriceWithoutVat(),
                    'price_with_vat' => $product->getAllVatPrices(), // Now using stored prices
                    'category' => $product->getCategory(),
                    'created_at' => $product->getCreatedAt()->format('Y-m-d H:i:s')
                ];
            }, $result->products),
            'pagination' => [
                'total' => $result->total,
                'page' => $result->page,
                'limit' => $result->limit,
                'pages' => ceil($result->total / $result->limit)
            ]
        ];

        return new JsonResponse($data);
    }

    #[Route('', name: 'products_create', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    #[OA\Post(
        path: '/api/products',
        summary: 'Create a new product',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(ref: '#/components/schemas/CreateProduct')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Product created', content: new OA\JsonContent(ref: '#/components/schemas/Product')),
            new OA\Response(response: 400, description: 'Invalid data')
        ]
    )]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name'], $data['description'], $data['price'], $data['category'])) {
            return new JsonResponse([
                'error' => 'Missing required fields: name, description, price, category'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $dto = new CreateProductDTO(
                $data['name'],
                $data['description'],
                (float) $data['price'],
                $data['category']
            );

            $product = $this->createProductUseCase->execute($dto);

            $responseData = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price_without_vat' => $product->getPriceWithoutVat(),
                'price_with_vat' => $product->getAllVatPrices(), // Now using stored prices
                'category' => $product->getCategory(),
                'created_at' => $product->getCreatedAt()->format('Y-m-d H:i:s')
            ];

            return new JsonResponse($responseData, Response::HTTP_CREATED);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
