<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\DataTransferObjects;

use AppModules\Client\src\DataTransferObjects\ClientDTO;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Models\Invoice;

final readonly class InvoiceDTO
{
    /**
     * @param  array<InvoiceItemDTO>  $items
     */
    public function __construct(
        public int $id,
        public string $invoiceNumber,
        public int $clientId,
        public ?ClientDTO $client,
        public InvoiceStatus $status,
        public ?\DateTime $issuedAt = null,
        public ?\DateTime $dueAt = null,
        public float $subtotal = 0.0,
        public float $taxAmount = 0.0,
        public float $discountAmount = 0.0,
        public float $total = 0.0,
        public ?string $notes = null,
        public ?string $terms = null,
        public array $items = [],
    ) {}

    /**
     * Create from Eloquent model.
     */
    public static function fromModel(Invoice $invoice): self
    {
        $client = $invoice->relationLoaded('client') && $invoice->client
            ? ClientDTO::fromModel($invoice->client)
            : null;

        $items = $invoice->relationLoaded('items')
            ? $invoice->items->map(fn ($item) => InvoiceItemDTO::fromModel($item))->toArray()
            : [];

        return new self(
            id: $invoice->id,
            invoiceNumber: $invoice->invoice_number,
            clientId: $invoice->client_id,
            client: $client,
            status: $invoice->status,
            issuedAt: $invoice->issued_at ? \DateTime::createFromInterface($invoice->issued_at) : null,
            dueAt: $invoice->due_at ? \DateTime::createFromInterface($invoice->due_at) : null,
            subtotal: (float) $invoice->subtotal,
            taxAmount: (float) $invoice->tax_amount,
            discountAmount: (float) $invoice->discount_amount,
            total: (float) $invoice->total,
            notes: $invoice->notes,
            terms: $invoice->terms,
            items: $items,
        );
    }
}
