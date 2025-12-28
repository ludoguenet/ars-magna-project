<?php

declare(strict_types=1);

namespace AppModules\Payment\src\Contracts;

use AppModules\Payment\src\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

interface PaymentRepositoryContract
{
    /**
     * Get all payments.
     *
     * @return Collection<int, Payment>
     */
    public function all(): Collection;

    /**
     * Find a payment by ID.
     */
    public function find(int $id): ?Payment;

    /**
     * Create a new payment.
     */
    public function create(array $data): Payment;

    /**
     * Update a payment.
     */
    public function update(Payment $payment, array $data): bool;

    /**
     * Delete a payment.
     */
    public function delete(Payment $payment): bool;

    /**
     * Get payments for an invoice.
     *
     * @return Collection<int, Payment>
     */
    public function getByInvoiceId(int $invoiceId): Collection;

    /**
     * Get pending payments.
     *
     * @return Collection<int, Payment>
     */
    public function getPending(): Collection;
}
