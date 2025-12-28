<?php

declare(strict_types=1);

use AppModules\Client\src\Models\Client;
use AppModules\Invoice\src\Actions\CreateInvoiceAction;
use AppModules\Invoice\src\DataTransferObjects\InvoiceDTO;
use AppModules\Invoice\src\Events\InvoiceCreated;
use AppModules\Payment\src\Jobs\SendPaymentReminderJob;
use AppModules\Payment\src\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('listens to InvoiceCreated event', function () {
    Event::fake();

    $client = Client::factory()->create();

    $invoiceData = new InvoiceDTO(
        clientId: $client->id,
        issuedAt: now(),
        dueAt: now()->addDays(30),
    );

    $createAction = app(CreateInvoiceAction::class);
    $invoice = $createAction->handle($invoiceData);

    Event::assertDispatched(InvoiceCreated::class, function ($event) use ($invoice) {
        return $event->invoice->id === $invoice->id;
    });
});

it('creates a payment record when invoice is created', function () {
    $client = Client::factory()->create();

    $invoiceData = new InvoiceDTO(
        clientId: $client->id,
        issuedAt: now(),
        dueAt: now()->addDays(30),
    );

    $createAction = app(CreateInvoiceAction::class);
    $invoice = $createAction->handle($invoiceData);

    // Refresh invoice to get calculated totals
    $invoice->refresh();

    // Verify payment was created
    $payment = Payment::where('invoice_id', $invoice->id)->first();

    expect($payment)
        ->not->toBeNull()
        ->invoice_id->toBe($invoice->id)
        ->amount->toBe((string) $invoice->total)
        ->status->toBe('pending')
        ->paid_at->toBeNull();
});

it('schedules payment reminder when invoice has due date', function () {
    Queue::fake();

    $client = Client::factory()->create();

    $invoiceData = new InvoiceDTO(
        clientId: $client->id,
        issuedAt: now(),
        dueAt: now()->addDays(30),
    );

    $createAction = app(CreateInvoiceAction::class);
    $invoice = $createAction->handle($invoiceData);

    // Verify reminder job was dispatched
    Queue::assertPushed(SendPaymentReminderJob::class, function ($job) use ($invoice) {
        return $job->invoiceId === $invoice->id;
    });
});

it('does not schedule payment reminder when invoice has no due date', function () {
    Queue::fake();

    $client = Client::factory()->create();

    $invoiceData = new InvoiceDTO(
        clientId: $client->id,
        issuedAt: now(),
        dueAt: null,
    );

    $createAction = app(CreateInvoiceAction::class);
    $invoice = $createAction->handle($invoiceData);

    // Verify reminder job was NOT dispatched
    Queue::assertNotPushed(SendPaymentReminderJob::class);
});

it('does not schedule payment reminder when reminder date is in the past', function () {
    Queue::fake();

    $client = Client::factory()->create();

    // Due date is only 3 days away, so reminder (7 days before) would be in the past
    $invoiceData = new InvoiceDTO(
        clientId: $client->id,
        issuedAt: now(),
        dueAt: now()->addDays(3),
    );

    $createAction = app(CreateInvoiceAction::class);
    $invoice = $createAction->handle($invoiceData);

    // Verify reminder job was NOT dispatched
    Queue::assertNotPushed(SendPaymentReminderJob::class);
});

it('creates payment with correct amount matching invoice total', function () {
    $client = Client::factory()->create();

    $invoiceData = new InvoiceDTO(
        clientId: $client->id,
        issuedAt: now(),
        dueAt: now()->addDays(30),
    );

    $createAction = app(CreateInvoiceAction::class);
    $invoice = $createAction->handle($invoiceData);

    // Refresh to get calculated totals
    $invoice->refresh();

    $payment = Payment::where('invoice_id', $invoice->id)->first();

    expect($payment)
        ->not->toBeNull()
        ->amount->toBe((string) $invoice->total);
});
