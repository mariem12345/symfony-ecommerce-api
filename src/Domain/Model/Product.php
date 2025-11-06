<?php

namespace App\Domain\Model;

use App\Domain\Exception\InvalidProductDataException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    private float $price;

    #[ORM\Column(type: 'string', length: 100)]
    private string $category;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $name,
        string $description,
        float $price,
        string $category
    ) {
        $this->validate($name, $price);

        $this->id = uniqid('prod_', true);
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        $this->category = $category;
        $this->createdAt = new \DateTimeImmutable();
    }

    private function validate(string $name, float $price): void
    {
        if (empty(trim($name))) {
            throw new InvalidProductDataException('Product name cannot be empty');
        }

        if ($price < 0) {
            throw new InvalidProductDataException('Product price cannot be negative');
        }
    }

    public function calculatePriceWithVat(int $vatRate): float
    {
        if ($vatRate < 0 || $vatRate > 100) {
            throw new InvalidProductDataException('VAT rate must be between 0 and 100');
        }

        return round($this->price * (1 + $vatRate / 100), 2);
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }
    public function getPrice(): float { return $this->price; }
    public function getCategory(): string { return $this->category; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
