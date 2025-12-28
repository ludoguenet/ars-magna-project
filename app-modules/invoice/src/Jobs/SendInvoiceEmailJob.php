<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\Jobs;

use AppModules\Invoice\src\Contracts\InvoiceRepositoryContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendInvoiceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $invoiceId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(InvoiceRepositoryContract $repository): void
    {
        $invoice = $repository->findModel($this->invoiceId);

        if (! $invoice) {
            \Illuminate\Support\Facades\Log::warning('Invoice not found for email sending', [
                'invoice_id' => $this->invoiceId,
            ]);

            return;
        }

        // TODO: Implement email sending
        // Example:
        // Mail::to($invoice->client->email)
        //     ->send(new InvoiceMail($invoice));
    }
}
