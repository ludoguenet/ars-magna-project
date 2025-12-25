<?php

declare(strict_types=1);

use AppModules\Product\src\DataTransferObjects\ProductDTO;
use AppModules\Product\src\Models\Product;
use AppModules\Product\src\Services\ProductService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can create a product using factory', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'sku' => 'SKU-123',
        'price' => 99.99,
    ]);

    expect($product)
        ->toBeInstanceOf(Product::class)
        ->name->toBe('Test Product')
        ->price->toBe('99.99')
        ->is_active->toBeTrue();

    $this->assertDatabaseHas('products', [
        'sku' => 'SKU-123',
        'name' => 'Test Product',
    ]);
});

it('can update a product', function () {
    $product = Product::factory()->create(['name' => 'Original Name']);

    $product->update([
        'name' => 'Updated Name',
        'price' => 149.99,
    ]);

    expect($product->refresh())
        ->name->toBe('Updated Name')
        ->price->toBe('149.99');

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Name',
    ]);
});

it('can delete a product', function () {
    $product = Product::factory()->create();

    $product->delete();

    $this->assertSoftDeleted('products', [
        'id' => $product->id,
    ]);
});

it('can get active products only', function () {
    Product::factory()->create(['is_active' => true]);
    Product::factory()->create(['is_active' => true]);
    Product::factory()->create(['is_active' => false]);

    $activeProducts = Product::where('is_active', true)->get();

    expect($activeProducts)->toHaveCount(2);
});

it('can search products by name', function () {
    Product::factory()->create(['name' => 'Product One', 'sku' => 'SKU-001']);
    Product::factory()->create(['name' => 'Product Two', 'sku' => 'SKU-002']);
    Product::factory()->create(['name' => 'Widget', 'sku' => 'SKU-003']);

    $results = Product::where('name', 'like', '%Product%')->get();

    expect($results)->toHaveCount(2);
});
