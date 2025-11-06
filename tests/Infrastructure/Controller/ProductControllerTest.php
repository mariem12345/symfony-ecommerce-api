<?php

namespace App\Tests\Infrastructure\Controller;

use App\Application\UseCase\CreateProductUseCase;
use App\Application\UseCase\ListProductsUseCase;
use App\Application\DTO\CreateProductDTO;
use App\Application\DTO\ProductListDTO;
use App\Domain\Model\Product;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductControllerTest extends TestCase
{
    private $createProductUseCase;
    private $listProductsUseCase;

    protected function setUp(): void
    {
        $this->createProductUseCase = $this->createMock(CreateProductUseCase::class);
        $this->listProductsUseCase = $this->createMock(ListProductsUseCase::class);
    }

    public function testListProductsReturnsJsonResponse(): void
    {
        $controller = new \App\Infrastructure\Controller\ProductController(
            $this->createProductUseCase,
            $this->listProductsUseCase
        );

        $products = [
            new Product('Product 1', 'Description 1', 100.00, 'electronics')
        ];

        $productListDTO = new ProductListDTO($products, 1, 1, 10);

        $this->listProductsUseCase
            ->method('execute')
            ->willReturn($productListDTO);

        $request = new Request(['page' => 1, 'limit' => 10]);

        $response = $controller->list($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testCreateProductReturnsJsonResponse(): void
    {
        $controller = new \App\Infrastructure\Controller\ProductController(
            $this->createProductUseCase,
            $this->listProductsUseCase
        );

        $product = new Product('New Product', 'Description', 150.00, 'books');

        $this->createProductUseCase
            ->method('execute')
            ->willReturn($product);

        $request = new Request([], [], [], [], [], [], json_encode([
            'name' => 'New Product',
            'description' => 'Description',
            'price' => 150.00,
            'category' => 'books'
        ]));

        $response = $controller->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
    }
}
