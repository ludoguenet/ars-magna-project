<?php

declare(strict_types=1);

use AppModules\Client\src\Models\Client;
use AppModules\Invoice\src\DataTransferObjects\InvoiceDTO;
use AppModules\Invoice\src\Models\Invoice;
use AppModules\Payment\src\Models\Payment;
use AppModules\Payment\src\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('creates a payment record for an invoice', function () {
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'total' => 1000.00,
    ]);

    $invoiceDTO = InvoiceDTO::fromModel($invoice->load(['client', 'items']));
    $service = app(PaymentService::class);
    $payment = $service->createPaymentForInvoice($invoiceDTO);

    expect($payment)
        ->toBeInstanceOf(Payment::class)
        ->invoice_id->toBe($invoice->id)
        ->amount->toBe('1000.00')
        ->status->toBe('pending')
        ->paid_at->toBeNull();
});

it('marks payment as completed', function () {
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
    ]);

    $payment = Payment::factory()->create([
        'invoice_id' => $invoice->id,
        'status' => 'pending',
    ]);

    $service = app(PaymentService::class);
    $result = $service->markAsCompleted($payment, 'credit_card');

    expect($result)->toBeTrue();

    $payment->refresh();

    expect($payment)
        ->status->toBe('completed')
        ->payment_method->toBe('credit_card')
        ->paid_at->not->toBeNull();
});

it('marks payment as failed', function () {
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
    ]);

    $payment = Payment::factory()->create([
        'invoice_id' => $invoice->id,
        'status' => 'pending',
    ]);

    $service = app(PaymentService::class);
    $result = $service->markAsFailed($payment, 'Insufficient funds');

    expect($result)->toBeTrue();

    $payment->refresh();

    expect($payment)
        ->status->toBe('failed')
        ->notes->toBe('Insufficient funds');
});
