<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\Listeners;

use AppModules\Invoice\src\Events\InvoiceFinalized;
use AppModules\Invoice\src\Jobs\GenerateInvoicePDFJob;
use AppModules\Invoice\src\Jobs\SendInvoiceEmailJob;
use Illuminate\Support\Facades\Log;

class HandleInvoiceFinalized
{
    /**
     * Handle the InvoiceFinalized event.
     *
     * This listener is triggered whenever an invoice is finalized (moved from DRAFT to SENT).
     * It generates the PDF and sends the invoice email to the client.
     */
    public function handle(InvoiceFinalized $event): void
    {
        $invoice = $event->invoice;

        // Log the event for debugging/monitoring
        Log::info('Invoice finalized - Processing PDF and email', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoiceNumber,
            'client_id' => $invoice->clientId,
            'total' => $invoice->total,
        ]);

        // 1. Generate PDF
        GenerateInvoicePDFJob::dispatch($invoice->id);

        Log::info('PDF generation job dispatched', [
            'invoice_id' => $invoice->id,
        ]);

        // 2. Send email to client
        SendInvoiceEmailJob::dispatch($invoice->id);

        Log::info('Invoice email job dispatched', [
            'invoice_id' => $invoice->id,
        ]);
    }
}
