<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\DataTransferObjects;

use AppModules\Client\src\DataTransferObjects\ClientDTO;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Models\Invoice;
use Illuminate\Http\Request;

final readonly class InvoiceDTO
{
    /**
     * @param  array<InvoiceItemDTO>  $items
     */
    public function __construct(
        public ?int $id = null,
        public string $invoiceNumber = '',
        public int $clientId = 0,
        public ?ClientDTO $client = null,
        public ?InvoiceStatus $status = null,
        public ?\DateTime $issuedAt = null,
        public ?\DateTime $dueAt = null,
        public float $subtotal = 0.0,
        public float $taxAmount = 0.0,
        public float $discountAmount = 0.0,
        public float $total = 0.0,
        public ?string $notes = null,
        public ?string $terms = null,
        public bool $shouldFinalize = false,
        public array $items = [],
    ) {}

    /**
     * Create from HTTP request.
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            clientId: $request->integer('client_id'),
            issuedAt: $request->filled('issued_at') ? new \DateTime($request->input('issued_at')) : null,
            dueAt: $request->filled('due_at') ? new \DateTime($request->input('due_at')) : null,
            notes: $request->input('notes'),
            terms: $request->input('terms'),
            shouldFinalize: $request->boolean('finalize', false),
        );
    }

    /**
     * Create from Eloquent model.
     */
    public static function fromModel(Invoice $invoice): self
    {
        $invoice->loadMissing(['client', 'items']);

        $client = $invoice->client
            ? ClientDTO::fromModel($invoice->client)
            : null;

        $items = $invoice->items->map(fn ($item) => InvoiceItemDTO::fromModel($item))->toArray();

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
