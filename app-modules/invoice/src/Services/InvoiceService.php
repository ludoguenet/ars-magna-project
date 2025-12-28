<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\Services;

use AppModules\Invoice\src\Actions\AddInvoiceItemAction;
use AppModules\Invoice\src\Actions\CalculateInvoiceTotalsAction;
use AppModules\Invoice\src\Actions\CreateInvoiceAction;
use AppModules\Invoice\src\Actions\FinalizeInvoiceAction;
use AppModules\Invoice\src\DataTransferObjects\InvoiceDTO;
use AppModules\Invoice\src\DataTransferObjects\InvoiceItemDTO;
use AppModules\Invoice\src\Models\Invoice;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        private CreateInvoiceAction $createInvoice,
        private AddInvoiceItemAction $addItem,
        private CalculateInvoiceTotalsAction $calculateTotals,
        private FinalizeInvoiceAction $finalize
    ) {}

    /**
     * Create a complete invoice with items.
     */
    public function createCompleteInvoice(
        InvoiceDTO $invoiceData,
        array $items
    ): Invoice {
        return DB::transaction(function () use ($invoiceData, $items) {
            $invoice = $this->createInvoice->handle($invoiceData);

            foreach ($items as $itemData) {
                $this->addItem->handle($invoice, InvoiceItemDTO::from($itemData));
            }

            $invoice->load('items');
            $this->calculateTotals->handle($invoice);

            if ($invoiceData->shouldFinalize) {
                $invoice = $this->finalize->handle($invoice);
            }

            return $invoice->fresh();
        });
    }

    /**
     * Add an item to an existing invoice.
     */
    public function addItemToInvoice(Invoice $invoice, InvoiceItemDTO $itemData): Invoice
    {
        return DB::transaction(function () use ($invoice, $itemData) {
            $this->addItem->handle($invoice, $itemData);
            $this->calculateTotals->handle($invoice);

            return $invoice->fresh();
        });
    }

    /**
     * Update a complete invoice with items.
     */
    public function updateCompleteInvoice(
        Invoice $invoice,
        InvoiceDTO $invoiceData,
        array $items
    ): Invoice {
        return DB::transaction(function () use ($invoice, $invoiceData, $items) {
            // Update invoice data
            $invoice->update([
                'client_id' => $invoiceData->clientId,
                'issued_at' => $invoiceData->issuedAt ?? $invoice->issued_at,
                'due_at' => $invoiceData->dueAt,
                'notes' => $invoiceData->notes,
                'terms' => $invoiceData->terms,
            ]);

            // Delete existing items
            $invoice->items()->delete();

            // Add new items
            foreach ($items as $itemData) {
                $this->addItem->handle($invoice, InvoiceItemDTO::from($itemData));
            }

            // Recalculate totals
            $invoice->load('items');
            $this->calculateTotals->handle($invoice);

            if ($invoiceData->shouldFinalize) {
                $invoice = $this->finalize->handle($invoice);
            }

            return $invoice->fresh();
        });
    }

    /**
     * Finalize an invoice.
     */
    public function finalizeInvoice(Invoice $invoice): Invoice
    {
        return DB::transaction(function () use ($invoice) {
            $this->calculateTotals->handle($invoice);

            return $this->finalize->handle($invoice);
        });
    }
}
