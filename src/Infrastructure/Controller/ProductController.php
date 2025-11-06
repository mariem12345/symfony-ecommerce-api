<?php

namespace App\Infrastructure\Controller;

use App\Application\UseCase\CreateProductUseCase;
use App\Application\UseCase\ListProductsUseCase;
use App\Application\DTO\CreateProductDTO;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/products')]
class ProductController
{
    public function __construct(
        private CreateProductUseCase $createProductUseCase,
        private ListProductsUseCase $listProductsUseCase
    ) {}

    #[Route('', name: 'products_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = max(1, min(100, (int) $request->query->get('limit', 10)));
        $name = $request->query->get('name');

        $filters = [];
        if ($name) {
            $filters['name'] = $name;
        }

        $result = $this->listProductsUseCase->execute($filters, $page, $limit);

        $data = [
            'data' => array_map(function ($product) {
                return [
                    'id' => $product->getId(),
                    'name' => $product->getName(),
                    'description' => $product->getDescription(),
                    'price' => $product->getPrice(),
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
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

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
                'price' => $product->getPrice(),
                'price_with_vat' => [
                    '4' => $product->calculatePriceWithVat(4),
                    '10' => $product->calculatePriceWithVat(10),
                    '21' => $product->calculatePriceWithVat(21)
                ],
                'category' => $product->getCategory(),
                'created_at' => $product->getCreatedAt()->format('Y-m-d H:i:s')
            ];

            return new JsonResponse($responseData, Response::HTTP_CREATED);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
