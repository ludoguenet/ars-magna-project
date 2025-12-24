<?php

namespace AppModules\Invoice\src\DataTransferObjects;

use AppModules\Invoice\src\Models\InvoiceItem;
use AppModules\Product\src\DataTransferObjects\ProductDTO;

final readonly class InvoiceItemDTO
{
    public function __construct(
        public int $id,
        public int $invoiceId,
        public ?int $productId,
        public ?ProductDTO $product,
        public string $description,
        public float $quantity,
        public float $unitPrice,
        public float $taxRate = 0.0,
        public float $discountAmount = 0.0,
        public float $lineTotal = 0.0,
    ) {}

    /**
     * Create from Eloquent model.
     */
    public static function fromModel(InvoiceItem $item): self
    {
        $product = $item->relationLoaded('product') && $item->product
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
