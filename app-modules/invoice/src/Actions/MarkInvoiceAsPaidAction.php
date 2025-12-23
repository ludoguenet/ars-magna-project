<?php

namespace AppModules\Invoice\src\Actions;

use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Events\InvoicePaid;
use AppModules\Invoice\src\Models\Invoice;

class MarkInvoiceAsPaidAction
{
    /**
     * Mark an invoice as paid.
     */
    public function handle(Invoice $invoice): Invoice
    {
        if (! $invoice->status->canBePaid()) {
            throw new \domainException('Invoice cannot be marked as paid in its current state.');
        }

        $invoice->update([
            'status' => InvoiceStatus::PAID,
        ]);

        event(new InvoicePaid($invoice));

        return $invoice->fresh();
    }
}
