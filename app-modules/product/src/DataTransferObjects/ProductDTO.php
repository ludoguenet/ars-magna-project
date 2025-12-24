<?php

namespace AppModules\Product\src\DataTransferObjects;

use AppModules\Product\src\Models\Product;

final readonly class ProductDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description = null,
        public ?string $sku = null,
        public float $price = 0.0,
        public float $taxRate = 0.0,
        public ?string $unit = null,
        public bool $isActive = true,
    ) {}

    /**
     * Create from Eloquent model.
     */
    public static function fromModel(Product $product): self
    {
        return new self(
            id: $product->id,
            name: $product->name,
            description: $product->description,
            sku: $product->sku,
            price: (float) $product->price,
            taxRate: (float) $product->tax_rate,
            unit: $product->unit,
            isActive: (bool) $product->is_active,
        );
    }
}
