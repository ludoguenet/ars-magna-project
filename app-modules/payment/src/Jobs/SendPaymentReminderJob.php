<?php

namespace AppModules\Payment\src\Jobs;

use AppModules\Invoice\src\Models\Invoice;
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
        public Invoice $invoice
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Log the reminder (in a real app, you'd send an email/notification)
        Log::info('Payment reminder sent', [
            'invoice_id' => $this->invoice->id,
            'invoice_number' => $this->invoice->invoice_number,
            'client_id' => $this->invoice->client_id,
            'amount' => $this->invoice->total,
            'due_date' => $this->invoice->due_at?->toDateString(),
        ]);

        // Example: Send email notification
        // Mail::to($this->invoice->client->email)
        //     ->send(new PaymentReminderMail($this->invoice));
    }
}
