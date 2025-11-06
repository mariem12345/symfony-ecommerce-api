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
    private float $priceWithoutVat;

    #[ORM\Column(name: 'price_with_vat4', type: 'decimal', precision: 10, scale: 2)]
    private float $priceWithVat4;

    #[ORM\Column(name: 'price_with_vat10', type: 'decimal', precision: 10, scale: 2)]
    private float $priceWithVat10;

    #[ORM\Column(name: 'price_with_vat21', type: 'decimal', precision: 10, scale: 2)]
    private float $priceWithVat21;

    #[ORM\Column(type: 'string', length: 100)]
    private string $category;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(
        string $name,
        string $description,
        float $priceWithoutVat,
        string $category
    ) {
        $this->validate($name, $priceWithoutVat);

        $this->id = uniqid('prod_', true);
        $this->name = $name;
        $this->description = $description;
        $this->priceWithoutVat = $priceWithoutVat;
        $this->category = $category;
        $this->createdAt = new \DateTimeImmutable();

        // Calculate and store all VAT prices
        $this->priceWithVat4 = $this->calculatePriceWithVat($priceWithoutVat, 4);
        $this->priceWithVat10 = $this->calculatePriceWithVat($priceWithoutVat, 10);
        $this->priceWithVat21 = $this->calculatePriceWithVat($priceWithoutVat, 21);
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

    private function calculatePriceWithVat(float $price, int $vatRate): float
    {
        if ($vatRate < 0 || $vatRate > 100) {
            throw new InvalidProductDataException('VAT rate must be between 0 and 100');
        }

        return round($price * (1 + $vatRate / 100), 2);
    }

    // Getters
    public function getId(): string { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getDescription(): string { return $this->description; }
    public function getPriceWithoutVat(): float { return $this->priceWithoutVat; }
    public function getPriceWithVat4(): float { return $this->priceWithVat4; }
    public function getPriceWithVat10(): float { return $this->priceWithVat10; }
    public function getPriceWithVat21(): float { return $this->priceWithVat21; }
    public function getCategory(): string { return $this->category; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }

    // Helper method to get all VAT prices
    public function getAllVatPrices(): array
    {
        return [
            '4' => $this->priceWithVat4,
            '10' => $this->priceWithVat10,
            '21' => $this->priceWithVat21
        ];
    }
}
