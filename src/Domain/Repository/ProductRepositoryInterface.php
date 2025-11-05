<?php

namespace App\Domain\Repository;

use App\Domain\Model\Product;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function findById(string $id): ?Product;
    public function findByCriteria(array $criteria, int $page = 1, int $limit = 10): array;
    public function countByCriteria(array $criteria): int;
}
