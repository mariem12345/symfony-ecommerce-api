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
            'Test Product', //name
            'Test description',  //description
            100.00, //price
            'electronics' //category
        );

        $this->assertEquals('Test Product', $product->getName());
        $this->assertEquals(100.00, $product->getPrice());
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

    public function testCalculatesVatPricesCorrectly(): void
    {
        $product = new Product(
            'Test Product',
            'Test description',
            100.00,
            'electronics'
        );

        $this->assertEquals(104.00, $product->calculatePriceWithVat(4));
        $this->assertEquals(110.00, $product->calculatePriceWithVat(10));
        $this->assertEquals(121.00, $product->calculatePriceWithVat(21));
    }
}
