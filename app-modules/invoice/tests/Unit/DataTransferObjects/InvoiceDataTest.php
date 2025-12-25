<?php

declare(strict_types=1);

use AppModules\Invoice\src\DataTransferObjects\InvoiceData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates InvoiceData from request with filled date fields', function () {
    $request = Request::create('/invoices', 'POST', [
        'client_id' => 1,
        'issued_at' => '2025-12-25',
        'due_at' => '2026-01-25',
        'notes' => 'Test notes',
        'terms' => 'Test terms',
    ]);

    $data = InvoiceData::fromRequest($request);

    expect($data->clientId)->toBe(1);
    expect($data->issuedAt)->toBeInstanceOf(\DateTime::class);
    expect($data->dueAt)->toBeInstanceOf(\DateTime::class);
    expect($data->notes)->toBe('Test notes');
    expect($data->terms)->toBe('Test terms');
});

it('creates InvoiceData from request with empty date fields', function () {
    $request = Request::create('/invoices', 'POST', [
        'client_id' => 1,
        'issued_at' => '',
        'due_at' => '',
        'notes' => 'Test notes',
        'terms' => 'Test terms',
    ]);

    $data = InvoiceData::fromRequest($request);

    expect($data->clientId)->toBe(1);
    expect($data->issuedAt)->toBeNull();
    expect($data->dueAt)->toBeNull();
    expect($data->notes)->toBe('Test notes');
    expect($data->terms)->toBe('Test terms');
});

it('creates InvoiceData from request with missing date fields', function () {
    $request = Request::create('/invoices', 'POST', [
        'client_id' => 1,
        'notes' => 'Test notes',
        'terms' => 'Test terms',
    ]);

    $data = InvoiceData::fromRequest($request);

    expect($data->clientId)->toBe(1);
    expect($data->issuedAt)->toBeNull();
    expect($data->dueAt)->toBeNull();
    expect($data->notes)->toBe('Test notes');
    expect($data->terms)->toBe('Test terms');
});

it('creates InvoiceData from request with only issued_at filled', function () {
    $request = Request::create('/invoices', 'POST', [
        'client_id' => 1,
        'issued_at' => '2025-12-25',
        'due_at' => '',
    ]);

    $data = InvoiceData::fromRequest($request);

    expect($data->clientId)->toBe(1);
    expect($data->issuedAt)->toBeInstanceOf(\DateTime::class);
    expect($data->dueAt)->toBeNull();
});

it('creates InvoiceData from request with only due_at filled', function () {
    $request = Request::create('/invoices', 'POST', [
        'client_id' => 1,
        'issued_at' => '',
        'due_at' => '2026-01-25',
    ]);

    $data = InvoiceData::fromRequest($request);

    expect($data->clientId)->toBe(1);
    expect($data->issuedAt)->toBeNull();
    expect($data->dueAt)->toBeInstanceOf(\DateTime::class);
});
