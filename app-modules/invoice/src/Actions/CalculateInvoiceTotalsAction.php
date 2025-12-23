<?php

namespace AppModules\Invoice\src\Actions;

use AppModules\Invoice\src\Models\Invoice;
use AppModules\Invoice\src\Models\InvoiceItem;
use AppModules\Shared\src\ValueObjects\Money;

class CalculateInvoiceTotalsAction
{
    /**
     * Calculate and update invoice totals.
     */
    public function handle(Invoice $invoice): Invoice
    {
        $subtotal = Money::fromDecimal(0);
        $totalTax = Money::fromDecimal(0);
        $totalDiscount = Money::fromDecimal(0);

        /** @var InvoiceItem $item */
        foreach ($invoice->items as $item) {
            $itemSubtotal = Money::fromDecimal((float) $item->unit_price)
                ->multiply((float) $item->quantity);

            if ($item->discount_amount > 0) {
                $discount = Money::fromDecimal((float) $item->discount_amount);
                $itemSubtotal = $itemSubtotal->subtract($discount);
                $totalDiscount = $totalDiscount->add($discount);
            }

            $subtotal = $subtotal->add($itemSubtotal);

            if ($item->tax_rate > 0) {
                $tax = $itemSubtotal->multiply((float) $item->tax_rate / 100);
                $totalTax = $totalTax->add($tax);
            }
        }

        $total = $subtotal->add($totalTax);

        $invoice->update([
            'subtotal' => $subtotal->toDecimal(),
            'tax_amount' => $totalTax->toDecimal(),
            'discount_amount' => $totalDiscount->toDecimal(),
            'total' => $total->toDecimal(),
        ]);

        return $invoice->fresh();
    }
}
