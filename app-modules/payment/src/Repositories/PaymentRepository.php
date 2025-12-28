<?php

declare(strict_types=1);

namespace AppModules\Payment\src\Repositories;

use AppModules\Payment\src\Contracts\PaymentRepositoryContract;
use AppModules\Payment\src\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository implements PaymentRepositoryContract
{
    /**
     * Get all payments.
     *
     * @return Collection<int, Payment>
     */
    public function all(): Collection
    {
        return Payment::with('invoice')->get();
    }

    /**
     * Find a payment by ID.
     */
    public function find(int $id): ?Payment
    {
        return Payment::with('invoice')->find($id);
    }

    /**
     * Create a new payment.
     */
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    /**
     * Update a payment.
     */
    public function update(Payment $payment, array $data): bool
    {
        return $payment->update($data);
    }

    /**
     * Delete a payment.
     */
    public function delete(Payment $payment): bool
    {
        return $payment->delete();
    }

    /**
     * Get payments for an invoice.
     *
     * @return Collection<int, Payment>
     */
    public function getByInvoiceId(int $invoiceId): Collection
    {
        return Payment::where('invoice_id', $invoiceId)->get();
    }

    /**
     * Get pending payments.
     *
     * @return Collection<int, Payment>
     */
    public function getPending(): Collection
    {
        return Payment::where('status', 'pending')->with('invoice')->get();
    }
}
