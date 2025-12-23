<?php

namespace AppModules\Invoice\src\Actions;

use AppModules\Invoice\src\DataTransferObjects\InvoiceData;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Events\InvoiceCreated;
use AppModules\Invoice\src\Models\Invoice;
use AppModules\Invoice\src\Repositories\InvoiceRepository;

class CreateInvoiceAction
{
    public function __construct(
        private InvoiceRepository $repository,
        private GenerateInvoiceNumberAction $generateNumber
    ) {}

    /**
     * Execute the action.
     */
    public function handle(InvoiceData $data): Invoice
    {
        $invoiceNumber = $this->generateNumber->handle($data->clientId);

        $invoice = $this->repository->create([
            'invoice_number' => $invoiceNumber,
            'client_id' => $data->clientId,
            'status' => InvoiceStatus::DRAFT,
            'issued_at' => $data->issuedAt ?? now(),
            'due_at' => $data->dueAt,
            'notes' => $data->notes,
            'terms' => $data->terms,
            'subtotal' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total' => 0,
        ]);

        event(new InvoiceCreated($invoice));

        return $invoice;
    }
}
