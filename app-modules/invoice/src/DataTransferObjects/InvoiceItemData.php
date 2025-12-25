<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\DataTransferObjects;

final readonly class InvoiceItemData
{
    public function __construct(
        public ?int $productId,
        public string $description,
        public float $quantity,
        public float $unitPrice,
        public float $taxRate = 0.0,
        public float $discountAmount = 0.0,
    ) {}

    /**
     * Create from array.
     */
    public static function from(array $data): self
    {
        return new self(
            productId: $data['product_id'] ?? null,
            description: $data['description'],
            quantity: (float) ($data['quantity'] ?? 1),
            unitPrice: (float) ($data['unit_price'] ?? 0),
            taxRate: (float) ($data['tax_rate'] ?? 0),
            discountAmount: (float) ($data['discount_amount'] ?? 0),
        );
    }
}
