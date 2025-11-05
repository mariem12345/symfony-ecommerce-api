<?php

namespace App\Tests\Application\UseCase;


use App\Domain\Model\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use PHPUnit\Framework\TestCase;

class ListProductsUseCaseTest extends TestCase
{
    private $productRepository;
    private ListProductsUseCase $useCase;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->useCase = new ListProductsUseCase($this->productRepository);
    }

    public function testListProductsWithPagination(): void
    {
        $products = [
            new Product('Product 1', 'Desc 1', 100.00, 'electronics'),
            new Product('Product 2', 'Desc 2', 200.00, 'electronics')
        ];

        $this->productRepository
            ->method('findByCriteria')
            ->with(['name' => 'Product'], 1, 10)
            ->willReturn($products);

        $this->productRepository
            ->method('countByCriteria')
            ->with(['name' => 'Product'])
            ->willReturn(2);

        $result = $this->useCase->execute(['name' => 'Product'], 1, 10);

        $this->assertInstanceOf(ProductListDTO::class, $result);
        $this->assertCount(2, $result->products);
        $this->assertEquals(2, $result->total);
    }
}
