<?php

namespace AppModules\Payment\src\Jobs;

use AppModules\Invoice\src\Contracts\InvoiceRepositoryContract;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendPaymentReminderJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $invoiceId
    ) {}

    /**
     * Execute the job.
     */
    public function handle(InvoiceRepositoryContract $invoiceRepository): void
    {
        $invoice = $invoiceRepository->find($this->invoiceId);

        if (! $invoice) {
            Log::warning('Payment reminder job: Invoice not found', [
                'invoice_id' => $this->invoiceId,
            ]);

            return;
        }

        // Log the reminder (in a real app, you'd send an email/notification)
        Log::info('Payment reminder sent', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoiceNumber,
            'client_id' => $invoice->clientId,
            'amount' => $invoice->total,
            'due_date' => $invoice->dueAt?->format('Y-m-d'),
        ]);

        // Example: Send email notification
        // Mail::to($invoice->client->email)
        //     ->send(new PaymentReminderMail($invoice));
    }
}
