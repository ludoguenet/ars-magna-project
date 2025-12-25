<?php

declare(strict_types=1);

use AppModules\Client\src\Models\Client;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Events\InvoiceFinalized;
use AppModules\Invoice\src\Models\Invoice;
use AppModules\Invoice\src\Models\InvoiceItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can finalize a draft invoice with items', function () {
    Event::fake();
    actingAs(\App\Models\User::factory()->create());

    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::DRAFT,
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'quantity' => 2,
        'unit_price' => 100.0,
        'tax_rate' => 20.0,
    ]);

    $response = post(route('invoice::finalize', $invoice->id));

    $response->assertRedirect(route('invoice::show', $invoice->id));
    $response->assertSessionHas('success', 'Invoice finalized and sent successfully');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::SENT);

    Event::assertDispatched(InvoiceFinalized::class, function ($event) use ($invoice) {
        return $event->invoice->id === $invoice->id;
    });
});

it('cannot finalize an invoice without items', function () {
    actingAs(\App\Models\User::factory()->create());

    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::DRAFT,
    ]);

    $response = post(route('invoice::finalize', $invoice->id));

    $response->assertRedirect(route('invoice::show', $invoice->id));
    $response->assertSessionHas('error', 'Cannot finalize an invoice without items.');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::DRAFT);
});

it('cannot finalize a non-draft invoice', function () {
    actingAs(\App\Models\User::factory()->create());

    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::SENT,
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
    ]);

    $response = post(route('invoice::finalize', $invoice->id));

    $response->assertRedirect(route('invoice::show', $invoice->id));
    $response->assertSessionHas('error');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::SENT);
});

it('shows finalize button only for draft invoices', function () {
    actingAs(\App\Models\User::factory()->create());

    $client = Client::factory()->create();

    // Draft invoice should show finalize button
    $draftInvoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::DRAFT,
    ]);

    InvoiceItem::factory()->create(['invoice_id' => $draftInvoice->id]);

    $response = $this->get(route('invoice::show', $draftInvoice->id));
    $response->assertSuccessful();
    $response->assertSee(route('invoice::finalize', $draftInvoice->id));
    $response->assertSee(route('invoice::edit', $draftInvoice->id));

    // Sent invoice should NOT show finalize button or edit button
    $sentInvoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::SENT,
    ]);

    InvoiceItem::factory()->create(['invoice_id' => $sentInvoice->id]);

    $response = $this->get(route('invoice::show', $sentInvoice->id));
    $response->assertSuccessful();
    $response->assertDontSee(route('invoice::finalize', $sentInvoice->id));
    $response->assertDontSee(route('invoice::edit', $sentInvoice->id));
});

it('returns 404 when finalizing non-existent invoice', function () {
    actingAs(\App\Models\User::factory()->create());

    $response = post(route('invoice::finalize', 99999));

    $response->assertNotFound();
});

it('recalculates totals before finalizing', function () {
    Event::fake();
    actingAs(\App\Models\User::factory()->create());

    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::DRAFT,
        'subtotal' => 0,
        'tax_amount' => 0,
        'total' => 0,
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'quantity' => 1,
        'unit_price' => 100.0,
        'tax_rate' => 20.0,
        'discount_amount' => 0.0,
    ]);

    post(route('invoice::finalize', $invoice->id));

    $invoice->refresh();
    expect((float) $invoice->subtotal)->toBe(100.0);
    expect((float) $invoice->tax_amount)->toBe(20.0);
    expect((float) $invoice->total)->toBe(120.0);
    expect($invoice->status)->toBe(InvoiceStatus::SENT);
});
