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
        public Invoice $invoice
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // TODO: Implement PDF generation using Snappy
        // Example:
        // $pdf = PDF::loadView('invoice::pdf.template', ['invoice' => $this->invoice]);
        // Storage::put("invoices/{$this->invoice->invoice_number}.pdf", $pdf->output());
    }
}
