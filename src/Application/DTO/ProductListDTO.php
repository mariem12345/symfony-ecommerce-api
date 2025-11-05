<?php

namespace App\Application\DTO;

readonly class ProductListDTO
{
    public function __construct(
        public array $products,
        public int   $total,
        public int   $page,
        public int   $limit
    ) {}
}
