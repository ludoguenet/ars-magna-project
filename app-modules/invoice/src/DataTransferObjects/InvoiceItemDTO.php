<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\DataTransferObjects;

use AppModules\Invoice\src\Models\InvoiceItem;
use AppModules\Product\src\DataTransferObjects\ProductDTO;

final readonly class InvoiceItemDTO
{
    public function __construct(
        public ?int $id = null,
        public ?int $invoiceId = null,
        public ?int $productId = null,
        public ?ProductDTO $product = null,
        public string $description = '',
        public float $quantity = 0.0,
        public float $unitPrice = 0.0,
        public float $taxRate = 0.0,
        public float $discountAmount = 0.0,
        public float $lineTotal = 0.0,
    ) {}

    /**
     * Create from array.
     */
    public static function from(array $data): self
    {
        return new self(
            productId: isset($data['product_id']) && $data['product_id'] !== '' ? (int) $data['product_id'] : null,
            description: $data['description'],
            quantity: (float) ($data['quantity'] ?? 1),
            unitPrice: (float) ($data['unit_price'] ?? 0),
            taxRate: (float) ($data['tax_rate'] ?? 0),
            discountAmount: (float) ($data['discount_amount'] ?? 0),
        );
    }

    /**
     * Create from Eloquent model.
     */
    public static function fromModel(InvoiceItem $item): self
    {
        $item->loadMissing('product');

        $product = $item->product
            ? ProductDTO::fromModel($item->product)
            : null;

        return new self(
            id: $item->id,
            invoiceId: $item->invoice_id,
            productId: $item->product_id,
            product: $product,
            description: $item->description,
            quantity: (float) $item->quantity,
            unitPrice: (float) $item->unit_price,
            taxRate: (float) $item->tax_rate,
            discountAmount: (float) $item->discount_amount,
            lineTotal: (float) $item->line_total,
        );
    }
}
