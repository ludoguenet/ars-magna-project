<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\Actions;

use AppModules\Invoice\src\Repositories\InvoiceRepository;

class GenerateInvoiceNumberAction
{
    public function __construct(
        private InvoiceRepository $repository
    ) {}

    /**
     * Generate a unique invoice number.
     */
    public function handle(?int $clientId = null): string
    {
        $year = now()->year;
        $prefix = config('invoice.number_prefix', 'FAC');

        // Get the last invoice number for this year
        $lastInvoice = $this->repository->getLastInvoiceForYear($year, $prefix);

        if ($lastInvoice && preg_match('/\d+$/', $lastInvoice->invoice_number, $matches)) {
            $nextNumber = (int) $matches[0] + 1;
        } else {
            $nextNumber = 1;
        }

        return sprintf('%s-%d-%04d', $prefix, $year, $nextNumber);
    }
}
