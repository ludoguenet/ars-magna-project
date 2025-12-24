<?php

declare(strict_types=1);

use AppModules\Client\src\Models\Client;
use AppModules\Invoice\src\Models\Invoice;
use AppModules\Payment\src\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

it('can create a payment', function () {
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
    ]);

    $payment = Payment::factory()->create([
        'invoice_id' => $invoice->id,
        'amount' => 500.00,
        'status' => 'pending',
    ]);

    expect($payment)
        ->toBeInstanceOf(Payment::class)
        ->invoice_id->toBe($invoice->id)
        ->amount->toBe('500.00')
        ->status->toBe('pending');
});

it('has relationship with invoice', function () {
    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
    ]);

    $payment = Payment::factory()->create([
        'invoice_id' => $invoice->id,
    ]);

    expect($payment->invoice)
        ->toBeInstanceOf(Invoice::class)
        ->id->toBe($invoice->id);
});

it('can check if payment is pending', function () {
    $payment = Payment::factory()->pending()->create();

    expect($payment->isPending())->toBeTrue();
    expect($payment->isCompleted())->toBeFalse();
    expect($payment->isFailed())->toBeFalse();
});

it('can check if payment is completed', function () {
    $payment = Payment::factory()->completed()->create();

    expect($payment->isPending())->toBeFalse();
    expect($payment->isCompleted())->toBeTrue();
    expect($payment->isFailed())->toBeFalse();
});

it('can check if payment is failed', function () {
    $payment = Payment::factory()->create([
        'status' => 'failed',
    ]);

    expect($payment->isPending())->toBeFalse();
    expect($payment->isCompleted())->toBeFalse();
    expect($payment->isFailed())->toBeTrue();
});
