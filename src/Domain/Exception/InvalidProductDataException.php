<?php

namespace App\Domain\Exception;

class InvalidProductDataException extends \InvalidArgumentException
{
    public function __construct(string $message = 'Invalid product data')
    {
        parent::__construct($message);
    }
}
