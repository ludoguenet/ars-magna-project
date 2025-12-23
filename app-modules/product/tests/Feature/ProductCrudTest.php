<?php

declare(strict_types=1);

use AppModules\Product\src\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can display the product index page', function () {
    Product::factory()->count(3)->create();

    get(route('product::index'))
        ->assertSuccessful()
        ->assertViewIs('product::index')
        ->assertSee('Produits')
        ->assertSee('Nouveau produit');
});

it('displays all products in the index', function () {
    $product1 = Product::factory()->create(['name' => 'Product One', 'price' => 100.00]);
    $product2 = Product::factory()->create(['name' => 'Product Two', 'price' => 200.00]);

    $response = get(route('product::index'))
        ->assertSuccessful();

    $response->assertSee('Product One');
    $response->assertSee('Product Two');
    $response->assertSee('100,00 €');
    $response->assertSee('200,00 €');
});

it('displays empty state when no products exist', function () {
    get(route('product::index'))
        ->assertSuccessful()
        ->assertSee('Aucun produit trouvé');
});

it('can display the product create page', function () {
    get(route('product::create'))
        ->assertSuccessful()
        ->assertViewIs('product::create')
        ->assertSee('Nouveau produit');
});

it('can create a new product', function () {
    $productData = [
        'name' => 'Test Product',
        'description' => 'Test description',
        'sku' => 'SKU-123',
        'price' => 99.99,
        'tax_rate' => 20.0,
        'unit' => 'piece',
        'is_active' => true,
    ];

    post(route('product::store'), $productData)
        ->assertRedirect()
        ->assertSessionHas('success', 'Produit créé avec succès');

    $product = Product::where('name', 'Test Product')->first();

    expect($product)
        ->not->toBeNull()
        ->name->toBe('Test Product')
        ->description->toBe('Test description')
        ->sku->toBe('SKU-123')
        ->price->toBe('99.99')
        ->tax_rate->toBe('20.00')
        ->unit->toBe('piece')
        ->is_active->toBeTrue();
});

it('can create a product with minimal required fields', function () {
    $productData = [
        'name' => 'Minimal Product',
        'price' => 50.00,
    ];

    post(route('product::store'), $productData)
        ->assertRedirect()
        ->assertSessionHas('success');

    $product = Product::where('name', 'Minimal Product')->first();

    expect($product)
        ->not->toBeNull()
        ->name->toBe('Minimal Product')
        ->price->toBe('50.00')
        ->description->toBeNull()
        ->sku->toBeNull()
        ->is_active->toBeTrue(); // Default from factory
});

it('validates required fields when creating a product', function () {
    post(route('product::store'), [])
        ->assertSessionHasErrors(['name', 'price']);
});

it('validates price is numeric and non-negative', function () {
    post(route('product::store'), [
        'name' => 'Test Product',
        'price' => -10,
    ])
        ->assertSessionHasErrors(['price']);

    post(route('product::store'), [
        'name' => 'Test Product',
        'price' => 'not-a-number',
    ])
        ->assertSessionHasErrors(['price']);
});

it('validates sku is unique', function () {
    Product::factory()->create(['sku' => 'SKU-123']);

    post(route('product::store'), [
        'name' => 'Test Product',
        'price' => 100.00,
        'sku' => 'SKU-123',
    ])
        ->assertSessionHasErrors(['sku']);
});

it('validates tax_rate is between 0 and 100', function () {
    post(route('product::store'), [
        'name' => 'Test Product',
        'price' => 100.00,
        'tax_rate' => 150,
    ])
        ->assertSessionHasErrors(['tax_rate']);
});

it('can display the product show page', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'price' => 99.99,
    ]);

    get(route('product::show', $product->id))
        ->assertSuccessful()
        ->assertViewIs('product::show')
        ->assertSee('Test Product')
        ->assertSee('99,99 €');
});

it('returns 404 for non-existent product', function () {
    get(route('product::show', 99999))
        ->assertNotFound();
});

it('can display the product edit page', function () {
    $product = Product::factory()->create([
        'name' => 'Test Product',
        'price' => 99.99,
    ]);

    get(route('product::edit', $product->id))
        ->assertSuccessful()
        ->assertViewIs('product::edit')
        ->assertSee('Modifier le produit')
        ->assertSee('Test Product', false);
});

it('returns 404 when editing non-existent product', function () {
    get(route('product::edit', 99999))
        ->assertNotFound();
});

it('can update an existing product', function () {
    $product = Product::factory()->create([
        'name' => 'Original Name',
        'price' => 50.00,
    ]);

    $updateData = [
        'name' => 'Updated Name',
        'description' => 'Updated description',
        'sku' => 'SKU-UPDATED',
        'price' => 150.00,
        'tax_rate' => 21.0,
        'unit' => 'kg',
        'is_active' => false,
    ];

    put(route('product::update', $product->id), $updateData)
        ->assertRedirect()
        ->assertSessionHas('success', 'Produit modifié avec succès');

    $product->refresh();

    expect($product)
        ->name->toBe('Updated Name')
        ->description->toBe('Updated description')
        ->sku->toBe('SKU-UPDATED')
        ->price->toBe('150.00')
        ->tax_rate->toBe('21.00')
        ->unit->toBe('kg')
        ->is_active->toBeFalse();
});

it('validates required fields when updating a product', function () {
    $product = Product::factory()->create();

    put(route('product::update', $product->id), [])
        ->assertSessionHasErrors(['name', 'price']);
});

it('validates sku uniqueness when updating (ignoring current product)', function () {
    $product1 = Product::factory()->create(['sku' => 'SKU-001']);
    $product2 = Product::factory()->create(['sku' => 'SKU-002']);

    // Should allow updating product2 with its own SKU
    put(route('product::update', $product2->id), [
        'name' => 'Updated Product',
        'price' => 100.00,
        'sku' => 'SKU-002',
    ])
        ->assertRedirect();

    // Should not allow updating product2 with product1's SKU
    put(route('product::update', $product2->id), [
        'name' => 'Updated Product',
        'price' => 100.00,
        'sku' => 'SKU-001',
    ])
        ->assertSessionHasErrors(['sku']);
});

it('can delete a product', function () {
    $product = Product::factory()->create(['name' => 'Product to Delete']);

    delete(route('product::destroy', $product->id))
        ->assertRedirect()
        ->assertSessionHas('success', 'Produit supprimé avec succès');

    expect(Product::find($product->id))->toBeNull();
    expect(Product::withTrashed()->find($product->id))->not->toBeNull();
});

it('handles deletion of non-existent product gracefully', function () {
    delete(route('product::destroy', 99999))
        ->assertRedirect()
        ->assertSessionHas('success');
});

it('displays product status correctly in index', function () {
    $activeProduct = Product::factory()->create(['is_active' => true]);
    $inactiveProduct = Product::factory()->create(['is_active' => false]);

    $response = get(route('product::index'))
        ->assertSuccessful();

    $response->assertSee('Actif');
    $response->assertSee('Inactif');
});

it('displays product details correctly in show page', function () {
    $product = Product::factory()->create([
        'name' => 'Detailed Product',
        'description' => 'Product description',
        'sku' => 'SKU-DETAIL',
        'price' => 199.99,
        'tax_rate' => 20.0,
        'unit' => 'piece',
        'is_active' => true,
    ]);

    $response = get(route('product::show', $product->id))
        ->assertSuccessful();

    $response->assertSee('Detailed Product');
    $response->assertSee('Product description');
    $response->assertSee('SKU-DETAIL');
    $response->assertSee('199,99 €');
    $response->assertSee('20,00%');
    $response->assertSee('piece');
    $response->assertSee('Actif');
});

it('handles optional fields correctly in show page', function () {
    $product = Product::factory()->create([
        'name' => 'Minimal Product',
        'price' => 50.00,
        'description' => null,
        'sku' => null,
        'unit' => null,
    ]);

    get(route('product::show', $product->id))
        ->assertSuccessful()
        ->assertSee('Minimal Product')
        ->assertSee('50,00 €');
});
