<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\Actions;

use AppModules\Invoice\src\DataTransferObjects\InvoiceDTO;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Events\InvoiceFinalized;
use AppModules\Invoice\src\Models\Invoice;

class FinalizeInvoiceAction
{
    /**
     * Finalize an invoice (change status from DRAFT to SENT).
     */
    public function handle(Invoice $invoice): Invoice
    {
        if (! $invoice->status->canBeFinalized()) {
            throw new \domainException('Invoice cannot be finalized in its current state.');
        }

        if ($invoice->items()->count() === 0) {
            throw new \domainException('Cannot finalize an invoice without items.');
        }

        $invoice->update([
            'status' => InvoiceStatus::SENT,
        ]);

        $invoice = $invoice->fresh()->load(['client', 'items']);
        $invoiceDTO = InvoiceDTO::fromModel($invoice);
        event(new InvoiceFinalized($invoiceDTO));

        return $invoice;
    }
}
