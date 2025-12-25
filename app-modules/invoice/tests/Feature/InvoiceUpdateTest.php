<?php

declare(strict_types=1);

use AppModules\Client\src\Models\Client;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Models\Invoice;
use AppModules\Invoice\src\Models\InvoiceItem;
use AppModules\Product\src\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\put;

use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can update an invoice with new items', function () {
    actingAs(\App\Models\User::factory()->create());

    $client = Client::factory()->create();
    $product = Product::factory()->create(['price' => 100.0, 'tax_rate' => 21.0]);

    // Create an invoice with one item
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::DRAFT,
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'description' => 'Old item',
        'quantity' => 1,
        'unit_price' => 50.0,
    ]);

    // Update with new items
    $response = put(route('invoice::update', $invoice->id), [
        'client_id' => $client->id,
        'issued_at' => now()->format('Y-m-d'),
        'due_at' => now()->addDays(30)->format('Y-m-d'),
        'notes' => 'Updated notes',
        'items' => [
            [
                'product_id' => $product->id,
                'description' => 'New item 1',
                'quantity' => 2,
                'unit_price' => 100.0,
                'tax_rate' => 21.0,
                'discount_amount' => 0,
            ],
            [
                'description' => 'New item 2',
                'quantity' => 1,
                'unit_price' => 50.0,
                'tax_rate' => 10.0,
                'discount_amount' => 0,
            ],
        ],
    ]);

    $response->assertRedirect(route('invoice::show', $invoice->id));

    // Verify invoice was updated
    $invoice->refresh();
    expect($invoice->notes)->toBe('Updated notes');
    expect($invoice->items)->toHaveCount(2);

    // Verify totals were recalculated
    // Item 1: 2 * 100 = 200, tax = 200 * 0.21 = 42, total = 242
    // Item 2: 1 * 50 = 50, tax = 50 * 0.10 = 5, total = 55
    // Subtotal: 200 + 50 = 250
    // Tax: 42 + 5 = 47
    // Total: 250 + 47 = 297
    expect((float) $invoice->subtotal)->toBe(250.0);
    expect((float) $invoice->tax_amount)->toBe(47.0);
    expect((float) $invoice->total)->toBe(297.0);
});

it('can update an invoice and preserve product association', function () {
    actingAs(\App\Models\User::factory()->create());

    $client = Client::factory()->create();
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'price' => 150.0,
        'tax_rate' => 20.0,
    ]);

    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::DRAFT,
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'product_id' => null,
        'description' => 'Old item',
    ]);

    // Update with product
    put(route('invoice::update', $invoice->id), [
        'client_id' => $client->id,
        'issued_at' => now()->format('Y-m-d'),
        'items' => [
            [
                'product_id' => $product->id,
                'description' => $product->name,
                'quantity' => 3,
                'unit_price' => $product->price,
                'tax_rate' => $product->tax_rate,
                'discount_amount' => 0,
            ],
        ],
    ]);

    $invoice->refresh();
    $item = $invoice->items->first();

    expect($item->product_id)->toBe($product->id);
    expect($item->description)->toBe('Test Product');
    expect((float) $item->unit_price)->toBe(150.0);
});

it('displays existing items in edit form', function () {
    actingAs(\App\Models\User::factory()->create());

    $client = Client::factory()->create();
    $invoice = Invoice::factory()->create([
        'client_id' => $client->id,
        'status' => InvoiceStatus::DRAFT,
    ]);

    InvoiceItem::factory()->create([
        'invoice_id' => $invoice->id,
        'description' => 'Existing item',
        'quantity' => 2,
        'unit_price' => 75.0,
        'tax_rate' => 15.0,
    ]);

    $response = $this->get(route('invoice::edit', $invoice->id));

    $response->assertSuccessful();
    $response->assertSee('Existing item');
    $response->assertSee('75');
    $response->assertSee('15');
});
