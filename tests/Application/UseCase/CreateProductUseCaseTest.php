<?php

namespace App\Tests\Application\UseCase;

use App\Application\UseCase\CreateProductUseCase;
use App\Application\DTO\CreateProductDTO;
use App\Domain\Model\Product;
use App\Domain\Repository\ProductRepositoryInterface;
use PHPUnit\Framework\TestCase;

class CreateProductUseCaseTest extends TestCase
{
    private $productRepository;
    private CreateProductUseCase $useCase;

    protected function setUp(): void
    {
        $this->productRepository = $this->createMock(ProductRepositoryInterface::class);
        $this->useCase = new CreateProductUseCase($this->productRepository);
    }

    public function testCreateProductSuccessfully(): void
    {
        $dto = new CreateProductDTO(
            'Test Product',
            'Test description',
            100.00,
            'electronics'
        );

        $this->productRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->isInstanceOf(Product::class));

        $product = $this->useCase->execute($dto);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals(100.00, $product->getPrice());
    }
}
