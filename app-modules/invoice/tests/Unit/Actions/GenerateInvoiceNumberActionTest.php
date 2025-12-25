<?php

declare(strict_types=1);

use AppModules\Client\src\Models\Client;
use AppModules\Invoice\src\Actions\GenerateInvoiceNumberAction;
use AppModules\Invoice\src\Models\Invoice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('generates a unique invoice number', function () {
    $action = app(GenerateInvoiceNumberAction::class);

    $invoiceNumber = $action->handle();

    expect($invoiceNumber)
        ->not->toBeEmpty()
        ->toBeString();
});

it('generates invoice numbers with year in format', function () {
    $action = app(GenerateInvoiceNumberAction::class);

    $invoiceNumber = $action->handle();
    $currentYear = now()->year;

    expect($invoiceNumber)
        ->toContain((string) $currentYear);
});

it('generates invoice numbers with expected format', function () {
    $action = app(GenerateInvoiceNumberAction::class);

    $number = $action->handle();

    // Should have format like FAC-YYYY-XXXX
    expect($number)
        ->toMatch('/^FAC-\d{4}-\d{4}$/');
});

it('generates unique invoice numbers even with existing invoices', function () {
    $client = Client::factory()->create();

    // Create some existing invoices
    Invoice::factory()->count(5)->create([
        'client_id' => $client->id,
    ]);

    $action = app(GenerateInvoiceNumberAction::class);
    $newNumber = $action->handle();

    // Verify the new number doesn't exist
    $exists = Invoice::where('invoice_number', $newNumber)->exists();
    expect($exists)->toBeFalse();
});

it('pads invoice numbers correctly', function () {
    $action = app(GenerateInvoiceNumberAction::class);

    $invoiceNumber = $action->handle();

    // Should have format like YYYY-00001 or similar
    expect(strlen($invoiceNumber))->toBeGreaterThanOrEqual(10);
});

it('generates unique invoice numbers considering soft-deleted invoices', function () {
    $client = Client::factory()->create();

    // Create and soft-delete an invoice with FAC-YYYY-0001
    $deletedInvoice = Invoice::factory()->create([
        'client_id' => $client->id,
    ]);
    $deletedInvoice->delete();

    // Generate a new invoice number
    $action = app(GenerateInvoiceNumberAction::class);
    $newNumber = $action->handle();

    // The new number should be different from the soft-deleted one
    expect($newNumber)->not->toBe($deletedInvoice->invoice_number);

    // Should be able to create a new invoice with the generated number without constraint violation
    $newInvoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'invoice_number' => $newNumber,
    ]);

    expect($newInvoice->invoice_number)->toBe($newNumber);
});
