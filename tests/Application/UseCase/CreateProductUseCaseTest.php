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
        $this->assertEquals(100.00, $product->getPriceWithoutVat()); // FIXED
        $this->assertEquals('electronics', $product->getCategory());
    }

    public function testCreateProductWithDifferentCategory(): void
    {
        $dto = new CreateProductDTO(
            'Book Product',
            'Book description',
            29.99,
            'books'
        );

        $this->productRepository
            ->expects($this->once())
            ->method('save');

        $product = $this->useCase->execute($dto);

        $this->assertEquals('Book Product', $product->getName());
        $this->assertEquals(29.99, $product->getPriceWithoutVat()); // FIXED
        $this->assertEquals('books', $product->getCategory());
    }

    public function testCreateProductStoresVatPrices(): void
    {
        $dto = new CreateProductDTO(
            'Test Product',
            'Test description',
            100.00,
            'electronics'
        );

        $this->productRepository
            ->expects($this->once())
            ->method('save');

        $product = $this->useCase->execute($dto);

        // Verify all VAT prices are stored
        $this->assertEquals(104.00, $product->getPriceWithVat4());
        $this->assertEquals(110.00, $product->getPriceWithVat10());
        $this->assertEquals(121.00, $product->getPriceWithVat21());

        $allPrices = $product->getAllVatPrices();
        $this->assertArrayHasKey('4', $allPrices);
        $this->assertArrayHasKey('10', $allPrices);
        $this->assertArrayHasKey('21', $allPrices);
    }
}
