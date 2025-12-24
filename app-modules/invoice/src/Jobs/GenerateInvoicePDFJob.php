<?php

namespace AppModules\Invoice\src\Jobs;

use AppModules\Invoice\src\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateInvoicePDFJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $invoiceId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $repository = app(\AppModules\Invoice\src\Repositories\InvoiceRepository::class);
        $invoice = $repository->findModel($this->invoiceId);

        if (! $invoice) {
            \Illuminate\Support\Facades\Log::warning('Invoice not found for PDF generation', [
                'invoice_id' => $this->invoiceId,
            ]);

            return;
        }

        // TODO: Implement PDF generation using Snappy
        // Example:
        // $pdf = PDF::loadView('invoice::pdf.template', ['invoice' => $invoice]);
        // Storage::put("invoices/{$invoice->invoice_number}.pdf", $pdf->output());
    }
}
