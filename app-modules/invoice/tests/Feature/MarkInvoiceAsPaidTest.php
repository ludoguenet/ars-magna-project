<?php

declare(strict_types=1);

use AppModules\Client\src\Models\Client;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Events\InvoicePaid;
use AppModules\Invoice\src\Models\Invoice;
use AppModules\Payment\src\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('can mark a sent invoice as paid', function () {
    Event::fake();

    $user = \App\Models\User::factory()->create();
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::SENT,
        'total' => 1000.00,
    ]);

    // Create a pending payment for the invoice
    $payment = Payment::factory()->create([
        'invoice_id' => $invoice->id,
        'amount' => $invoice->total,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->post(route('invoice::mark-as-paid', $invoice->id));

    $response->assertRedirect(route('invoice::show', $invoice->id));
    $response->assertSessionHas('success', 'Invoice marked as paid successfully');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::PAID);

    Event::assertDispatched(InvoicePaid::class, function ($event) use ($invoice) {
        return $event->invoice->id === $invoice->id;
    });
});

it('can mark an overdue invoice as paid', function () {
    Event::fake();

    $user = \App\Models\User::factory()->create();
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::SENT,
        'due_at' => now()->subDays(5),
        'total' => 1500.00,
    ]);

    // Manually mark as overdue
    $invoice->update(['status' => InvoiceStatus::OVERDUE]);

    $payment = Payment::factory()->create([
        'invoice_id' => $invoice->id,
        'amount' => $invoice->total,
        'status' => 'pending',
    ]);

    $response = $this->actingAs($user)->post(route('invoice::mark-as-paid', $invoice->id));

    $response->assertRedirect(route('invoice::show', $invoice->id));
    $response->assertSessionHas('success', 'Invoice marked as paid successfully');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::PAID);
});

it('prevents marking a draft invoice as paid', function () {
    $user = \App\Models\User::factory()->create();
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::DRAFT,
    ]);

    $response = $this->actingAs($user)->post(route('invoice::mark-as-paid', $invoice->id));

    $response->assertRedirect(route('invoice::show', $invoice->id));
    $response->assertSessionHas('error');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::DRAFT);
});

it('prevents marking an already paid invoice as paid', function () {
    $user = \App\Models\User::factory()->create();
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::PAID,
    ]);

    $response = $this->actingAs($user)->post(route('invoice::mark-as-paid', $invoice->id));

    $response->assertRedirect(route('invoice::show', $invoice->id));
    $response->assertSessionHas('error');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::PAID);
});

it('prevents marking a cancelled invoice as paid', function () {
    $user = \App\Models\User::factory()->create();
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::CANCELLED,
    ]);

    $response = $this->actingAs($user)->post(route('invoice::mark-as-paid', $invoice->id));

    $response->assertRedirect(route('invoice::show', $invoice->id));
    $response->assertSessionHas('error');

    $invoice->refresh();
    expect($invoice->status)->toBe(InvoiceStatus::CANCELLED);
});

it('updates payment record when invoice is marked as paid', function () {
    Event::fake();

    $user = \App\Models\User::factory()->create();
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::SENT,
        'total' => 2000.00,
    ]);

    $payment = Payment::factory()->create([
        'invoice_id' => $invoice->id,
        'amount' => $invoice->total,
        'status' => 'pending',
        'paid_at' => null,
    ]);

    // Mark invoice as paid
    $this->actingAs($user)->post(route('invoice::mark-as-paid', $invoice->id));

    // Manually trigger the listener to update payment
    // (In real app, this happens automatically via event)
    Event::assertDispatched(InvoicePaid::class);

    // Simulate the event listener behavior
    $listener = app(\AppModules\Payment\src\Listeners\HandleInvoicePaid::class);
    $invoiceDTO = \AppModules\Invoice\src\DataTransferObjects\InvoiceDTO::fromModel($invoice->fresh()->load(['client', 'items']));
    $listener->handle(new InvoicePaid($invoiceDTO));

    $payment->refresh();
    expect($payment->status)->toBe('completed')
        ->and($payment->paid_at)->not->toBeNull();
});

it('returns 404 when trying to mark a non-existent invoice as paid', function () {
    $user = \App\Models\User::factory()->create();
    $response = $this->actingAs($user)->post(route('invoice::mark-as-paid', 99999));

    $response->assertNotFound();
});
