<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\Contracts;

use AppModules\Invoice\src\DataTransferObjects\InvoiceDTO;

interface InvoiceRepositoryContract
{
    /**
     * Get all invoices.
     *
     * @return array<InvoiceDTO>
     */
    public function all(): array;

    /**
     * Find an invoice by ID.
     */
    public function find(int $id): ?InvoiceDTO;

    /**
     * Get total count of invoices.
     */
    public function count(): int;

    /**
     * Get total revenue from paid invoices.
     */
    public function getTotalRevenue(): float;

    /**
     * Get recent invoices.
     *
     * @return array<InvoiceDTO>
     */
    public function getRecentInvoices(int $limit = 5): array;

    /**
     * Get count of pending invoices.
     */
    public function getPendingCount(): int;

    /**
     * Get count of overdue invoices.
     */
    public function getOverdueCount(): int;

    /**
     * Delete an invoice by ID.
     */
    public function deleteById(int $id): bool;
}
