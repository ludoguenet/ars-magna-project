<?php

declare(strict_types=1);

namespace AppModules\Invoice\src\DataTransferObjects;

use Illuminate\Http\Request;

final readonly class InvoiceData
{
    public function __construct(
        public int $clientId,
        public ?\DateTime $issuedAt = null,
        public ?\DateTime $dueAt = null,
        public ?string $notes = null,
        public ?string $terms = null,
        public bool $shouldFinalize = false,
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
}
