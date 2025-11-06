<?php

namespace App\Tests\Domain\Model;

use App\Domain\Model\Product;
use App\Domain\Exception\InvalidProductDataException;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testCanCreateProductWithValidData(): void
    {
        $product = new Product(
            'Test Product',
            'Test description',
            100.00,
            'electronics'
        );

        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals(100.00, $product->getPriceWithoutVat()); // FIXED
        $this->assertEquals('electronics', $product->getCategory());
        $this->assertNotEmpty($product->getId());
        $this->assertInstanceOf(\DateTimeImmutable::class, $product->getCreatedAt());
    }

    public function testThrowsExceptionWhenPriceIsNegative(): void
    {
        $this->expectException(InvalidProductDataException::class);
        $this->expectExceptionMessage('Product price cannot be negative');

        new Product(
            'Test Product',
            'Test description',
            -50.00,
            'electronics'
        );
    }

    public function testThrowsExceptionWhenNameIsEmpty(): void
    {
        $this->expectException(InvalidProductDataException::class);
        $this->expectExceptionMessage('Product name cannot be empty');

        new Product(
            '',
            'Test description',
            50.00,
            'electronics'
        );
    }

    public function testStoresAllVatPrices(): void
    {
        $product = new Product(
            'Test Product',
            'Test description',
            100.00,
            'electronics'
        );

        $this->assertEquals(100.00, $product->getPriceWithoutVat());
        $this->assertEquals(104.00, $product->getPriceWithVat4());
        $this->assertEquals(110.00, $product->getPriceWithVat10());
        $this->assertEquals(121.00, $product->getPriceWithVat21());

        $allPrices = $product->getAllVatPrices();
        $this->assertEquals(104.00, $allPrices['4']);
        $this->assertEquals(110.00, $allPrices['10']);
        $this->assertEquals(121.00, $allPrices['21']);
    }

    public function testCalculatesVatPricesCorrectly(): void
    {
        $product = new Product(
            'Test Product',
            'Test description',
            100.00,
            'electronics'
        );

        // These should now return the stored prices, not calculate on the fly
        $this->assertEquals(104.00, $product->getPriceWithVat4());
        $this->assertEquals(110.00, $product->getPriceWithVat10());
        $this->assertEquals(121.00, $product->getPriceWithVat21());
    }

    public function testProductWithDecimalPrices(): void
    {
        $product = new Product(
            'Test Product',
            'Test description',
            99.99,
            'electronics'
        );

        $this->assertEquals(99.99, $product->getPriceWithoutVat());
        $this->assertEquals(103.99, $product->getPriceWithVat4()); // 99.99 * 1.04
        $this->assertEquals(109.99, $product->getPriceWithVat10()); // 99.99 * 1.10
        $this->assertEquals(120.99, $product->getPriceWithVat21()); // 99.99 * 1.21
    }
}
