<?php

declare(strict_types=1);

namespace AppModules\Payment\src\Listeners;

use AppModules\Invoice\src\Events\InvoiceCreated;
use AppModules\Payment\src\Jobs\SendPaymentReminderJob;
use AppModules\Payment\src\Services\PaymentService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class HandleInvoiceCreated
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Handle the InvoiceCreated event.
     *
     * This listener is triggered whenever a new invoice is created.
     * It creates a payment record and schedules a payment reminder.
     */
    public function handle(InvoiceCreated $event): void
    {
        $invoice = $event->invoice;

        // Log the event for debugging/monitoring
        Log::info('Invoice created - Payment module notified', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoiceNumber,
            'client_id' => $invoice->clientId,
            'total' => $invoice->total,
            'status' => $invoice->status->value,
        ]);

        // 1. Create a payment record
        $payment = $this->paymentService->createPaymentForInvoice($invoice);

        Log::info('Payment record created', [
            'payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'amount' => $payment->amount,
            'status' => $payment->status,
        ]);

        // 2. Schedule a payment reminder if due date exists
        if ($invoice->dueAt) {
            // Schedule reminder 7 days before due date
            $reminderDate = Carbon::instance($invoice->dueAt)->copy()->subDays(7);

            // Only schedule if reminder date is in the future
            if ($reminderDate->isFuture()) {
                SendPaymentReminderJob::dispatch($invoice->id)
                    ->delay($reminderDate);

                Log::info('Payment reminder scheduled', [
                    'invoice_id' => $invoice->id,
                    'reminder_date' => $reminderDate->format('Y-m-d'),
                    'due_date' => Carbon::instance($invoice->dueAt)->format('Y-m-d'),
                ]);
            }
        }
    }
}
