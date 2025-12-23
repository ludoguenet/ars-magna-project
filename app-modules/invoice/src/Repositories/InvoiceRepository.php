<?php

namespace AppModules\Invoice\src\Repositories;

use AppModules\Invoice\src\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository
{
    /**
     * Get all invoices.
     */
    public function all(): Collection
    {
        return Invoice::with(['client', 'items'])->get();
    }

    /**
     * Find an invoice by ID.
     */
    public function find(int $id): ?Invoice
    {
        return Invoice::with(['client', 'items'])->find($id);
    }

    /**
     * Create a new invoice.
     */
    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }

    /**
     * Update an invoice.
     */
    public function update(Invoice $invoice, array $data): bool
    {
        return $invoice->update($data);
    }

    /**
     * Delete an invoice.
     */
    public function delete(Invoice $invoice): bool
    {
        return $invoice->delete();
    }

    /**
     * Get the last invoice for a given year.
     */
    public function getLastInvoiceForYear(int $year): ?Invoice
    {
        return Invoice::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
    }
}
