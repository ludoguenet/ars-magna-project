<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\Actions;

use AppModules\Invoice\src\DataTransferObjects\InvoiceItemData;
use AppModules\Invoice\src\Models\Invoice;
use AppModules\Invoice\src\Models\InvoiceItem;
use AppModules\Shared\src\ValueObjects\Money;

class AddInvoiceItemAction
{
    /**
     * Add an item to an invoice.
     */
    public function handle(Invoice $invoice, InvoiceItemData $itemData): InvoiceItem
    {
        $lineTotal = $this->calculateLineTotal($itemData);

        $item = $invoice->items()->create([
            'product_id' => $itemData->productId,
            'description' => $itemData->description,
            'quantity' => $itemData->quantity,
            'unit_price' => $itemData->unitPrice,
            'tax_rate' => $itemData->taxRate,
            'discount_amount' => $itemData->discountAmount,
            'line_total' => $lineTotal->toDecimal(),
        ]);

        /** @var InvoiceItem $item */
        return $item;
    }

    /**
     * Calculate the line total for an item.
     */
    private function calculateLineTotal(InvoiceItemData $itemData): Money
    {
        $subtotal = Money::fromDecimal($itemData->unitPrice)
            ->multiply($itemData->quantity);

        if ($itemData->discountAmount > 0) {
            $subtotal = $subtotal->subtract(
                Money::fromDecimal($itemData->discountAmount)
            );
        }

        if ($itemData->taxRate > 0) {
            $taxAmount = $subtotal->multiply($itemData->taxRate / 100);

            return $subtotal->add($taxAmount);
        }

        return $subtotal;
    }
}
