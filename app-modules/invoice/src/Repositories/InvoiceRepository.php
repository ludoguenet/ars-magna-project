<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\Repositories;

use AppModules\Invoice\src\Contracts\InvoiceRepositoryContract;
use AppModules\Invoice\src\DataTransferObjects\InvoiceDTO;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Models\Invoice;

class InvoiceRepository implements InvoiceRepositoryContract
{
    /**
     * Get all invoices.
     *
     * @return array<InvoiceDTO>
     */
    public function all(): array
    {
        return Invoice::with(['client', 'items'])
            ->get()
            ->map(fn (Invoice $invoice) => InvoiceDTO::fromModel($invoice))
            ->toArray();
    }

    /**
     * Find an invoice by ID.
     */
    public function find(int $id): ?InvoiceDTO
    {
        $invoice = Invoice::with(['client', 'items'])->find($id);

        return $invoice ? InvoiceDTO::fromModel($invoice) : null;
    }

    /**
     * Get total count of invoices.
     */
    public function count(): int
    {
        return Invoice::count();
    }

    /**
     * Get total revenue from paid invoices.
     */
    public function getTotalRevenue(): float
    {
        return (float) Invoice::where('status', InvoiceStatus::PAID)->sum('total');
    }

    /**
     * Get recent invoices.
     *
     * @return array<InvoiceDTO>
     */
    public function getRecentInvoices(int $limit = 5): array
    {
        return Invoice::with(['client'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (Invoice $invoice) => InvoiceDTO::fromModel($invoice))
            ->toArray();
    }

    /**
     * Get count of pending invoices.
     */
    public function getPendingCount(): int
    {
        return Invoice::where('status', InvoiceStatus::SENT)->count();
    }

    /**
     * Get count of overdue invoices.
     */
    public function getOverdueCount(): int
    {
        return Invoice::overdue()->count();
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
     * Delete an invoice by ID.
     */
    public function deleteById(int $id): bool
    {
        $invoice = Invoice::find($id);

        return $invoice ? $invoice->delete() : false;
    }

    /**
     * Get the last invoice for a given year and prefix.
     */
    public function getLastInvoiceForYear(int $year, string $prefix = 'FAC'): ?Invoice
    {
        return Invoice::withTrashed()
            ->where('invoice_number', 'LIKE', "{$prefix}-{$year}-%")
            ->orderByRaw('CAST(SUBSTR(invoice_number, LENGTH(invoice_number) - 3) AS INTEGER) DESC')
            ->first();
    }

    /**
     * Find an invoice model by ID (internal use only).
     */
    public function findModel(int $id): ?Invoice
    {
        return Invoice::with(['client', 'items'])->find($id);
    }
}
