<?php

namespace App\Application\UseCase;

use App\Application\DTO\CreateProductDTO;
use App\Domain\Model\Product;
use App\Domain\Repository\ProductRepositoryInterface;

class CreateProductUseCase
{
    public function __construct(
        private ProductRepositoryInterface $productRepository
    ) {}

    public function execute(CreateProductDTO $dto): Product
    {
        $product = new Product(
            $dto->name,
            $dto->description,
            $dto->price,
            $dto->category
        );

        $this->productRepository->save($product);

        return $product;
    }
}
