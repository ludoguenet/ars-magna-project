<?php

namespace AppModules\Invoice\src\Services;

use AppModules\Invoice\src\Actions\AddInvoiceItemAction;
use AppModules\Invoice\src\Actions\CalculateInvoiceTotalsAction;
use AppModules\Invoice\src\Actions\CreateInvoiceAction;
use AppModules\Invoice\src\Actions\FinalizeInvoiceAction;
use AppModules\Invoice\src\DataTransferObjects\InvoiceData;
use AppModules\Invoice\src\DataTransferObjects\InvoiceItemData;
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
        InvoiceData $invoiceData,
        array $items
    ): Invoice {
        return DB::transaction(function () use ($invoiceData, $items) {
            $invoice = $this->createInvoice->handle($invoiceData);

            foreach ($items as $itemData) {
                $this->addItem->handle($invoice, InvoiceItemData::from($itemData));
            }

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
    public function addItemToInvoice(Invoice $invoice, InvoiceItemData $itemData): Invoice
    {
        return DB::transaction(function () use ($invoice, $itemData) {
            $this->addItem->handle($invoice, $itemData);
            $this->calculateTotals->handle($invoice);

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
