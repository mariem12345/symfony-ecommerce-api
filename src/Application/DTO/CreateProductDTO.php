<?php

namespace App\Application\DTO;

readonly class CreateProductDTO
{
    public function __construct(
        public string $name,
        public string $description,
        public float  $price,
        public string $category
    ) {}
}
