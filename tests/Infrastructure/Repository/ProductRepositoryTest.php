<?php

namespace App\Tests\Infrastructure\Repository;

use App\Domain\Model\Product;
use App\Infrastructure\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

class ProductRepositoryTest extends TestCase
{
    private $entityManager;
    private ProductRepository $repository;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->repository = new ProductRepository($this->entityManager);
    }

    public function testSaveProduct(): void
    {
        $product = new Product('Test Product', 'Description', 100.00, 'electronics');

        $this->entityManager
            ->expects($this->once())
            ->method('persist')
            ->with($product);

        $this->entityManager
            ->expects($this->once())
            ->method('flush');

        $this->repository->save($product);
    }

    public function testFindByIdReturnsProduct(): void
    {
        $product = new Product('Test Product', 'Description', 100.00, 'electronics');

        $this->entityManager
            ->method('find')
            ->with(Product::class, 'test-id')
            ->willReturn($product);

        $result = $this->repository->findById('test-id');

        $this->assertSame($product, $result);
    }

    public function testFindByIdReturnsNullWhenNotFound(): void
    {
        $this->entityManager
            ->method('find')
            ->with(Product::class, 'non-existent-id')
            ->willReturn(null);

        $result = $this->repository->findById('non-existent-id');

        $this->assertNull($result);
    }
}
