<?php

namespace App\Application\UseCase;

use App\Application\DTO\ProductListDTO;
use App\Domain\Repository\ProductRepositoryInterface;

class ListProductsUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(array $filters = [], int $page = 1, int $limit = 10): ProductListDTO
    {
        $products = $this->productRepository->findByCriteria($filters, $page, $limit);
        $total = $this->productRepository->countByCriteria($filters);

        return new ProductListDTO($products, $total, $page, $limit);
    }
}
