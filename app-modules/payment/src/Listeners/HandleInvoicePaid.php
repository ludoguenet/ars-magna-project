<?php

declare(strict_types=1);

namespace AppModules\Payment\src\Listeners;

use AppModules\Invoice\src\Events\InvoicePaid;
use AppModules\Payment\src\Contracts\PaymentRepositoryContract;
use AppModules\Payment\src\Services\PaymentService;
use Illuminate\Support\Facades\Log;

class HandleInvoicePaid
{
    public function __construct(
        private PaymentRepositoryContract $paymentRepository,
        private PaymentService $paymentService
    ) {}

    /**
     * Handle the InvoicePaid event.
     *
     * This listener is triggered when an invoice is marked as paid.
     * It updates the payment record to mark it as completed.
     */
    public function handle(InvoicePaid $event): void
    {
        $invoice = $event->invoice;

        Log::info('Invoice paid - Payment module notified', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoiceNumber,
            'total' => $invoice->total,
        ]);

        // Find the payment record for this invoice
        $payments = $this->paymentRepository->getByInvoiceId($invoice->id);

        // Get the pending payment (there should typically be one)
        /** @var \AppModules\Payment\src\Models\Payment|null $pendingPayment */
        $pendingPayment = $payments->first(fn (\AppModules\Payment\src\Models\Payment $payment) => $payment->isPending());

        if ($pendingPayment) {
            // Mark the payment as completed
            $this->paymentService->markAsCompleted($pendingPayment);

            Log::info('Payment marked as completed', [
                'payment_id' => $pendingPayment->id,
                'invoice_id' => $invoice->id,
                'amount' => $pendingPayment->amount,
            ]);
        } else {
            Log::warning('No pending payment found for invoice', [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoiceNumber,
            ]);
        }
    }
}
