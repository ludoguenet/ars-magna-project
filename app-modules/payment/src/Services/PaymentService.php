<?php

namespace AppModules\Payment\src\Services;

use AppModules\Invoice\src\Models\Invoice;
use AppModules\Payment\src\Repositories\PaymentRepository;

class PaymentService
{
    public function __construct(
        private PaymentRepository $repository
    ) {}

    /**
     * Create a payment record for an invoice.
     */
    public function createPaymentForInvoice(Invoice $invoice): \AppModules\Payment\src\Models\Payment
    {
        return $this->repository->create([
            'invoice_id' => $invoice->id,
            'amount' => $invoice->total,
            'status' => 'pending',
        ]);
    }

    /**
     * Mark payment as completed.
     */
    public function markAsCompleted(\AppModules\Payment\src\Models\Payment $payment, ?string $paymentMethod = null): bool
    {
        return $this->repository->update($payment, [
            'status' => 'completed',
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark payment as failed.
     */
    public function markAsFailed(\AppModules\Payment\src\Models\Payment $payment, ?string $notes = null): bool
    {
        return $this->repository->update($payment, [
            'status' => 'failed',
            'notes' => $notes,
        ]);
    }
}
