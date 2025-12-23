<?php

use AppModules\Client\src\Models\Client;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Models\Invoice;
use AppModules\Invoice\src\Models\InvoiceItem;
use AppModules\Product\src\Models\Product;
use Illuminate\Support\Facades\View;

use function Pest\Laravel\get;

describe('Layout Views', function () {
    it('renders app layout without errors', function () {
        $view = View::make('layouts.app');

        $html = $view->render();
        expect($html)->toContain('Dashboard');
        expect($html)->toContain('Factures');
    });

    it('renders welcome page without errors', function () {
        get('/')
            ->assertRedirect(route('dashboard'));
    });
});

describe('Dashboard Views', function () {
    it('renders dashboard index view with stats', function () {
        Client::factory()->count(3)->create();
        Product::factory()->count(5)->create();
        Invoice::factory()->count(2)->create();

        get(route('dashboard'))
            ->assertSuccessful()
            ->assertViewIs('dashboard::index')
            ->assertSee('Dashboard')
            ->assertSee('Total Clients')
            ->assertSee('Total Products')
            ->assertSee('Total Invoices');
    });

    it('renders dashboard with recent invoices', function () {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'status' => InvoiceStatus::SENT,
        ]);

        get(route('dashboard'))
            ->assertSuccessful()
            ->assertSee($invoice->invoice_number);
    });
});

describe('Client Views', function () {
    it('renders client index view', function () {
        $client = Client::factory()->create();

        get(route('client::index'))
            ->assertSuccessful()
            ->assertViewIs('client::index')
            ->assertSee('Clients')
            ->assertSee($client->name);
    });

    it('renders client create view', function () {
        get(route('client::create'))
            ->assertSuccessful()
            ->assertViewIs('client::create')
            ->assertSee('Nouveau client');
    });

    it('renders client edit view', function () {
        $client = Client::factory()->create();

        get(route('client::edit', $client->id))
            ->assertSuccessful()
            ->assertViewIs('client::edit')
            ->assertSee('Modifier le client')
            ->assertSee($client->name);
    });

    it('renders client show view', function () {
        $client = Client::factory()->create([
            'name' => 'Test Client',
            'email' => 'test@example.com',
        ]);

        get(route('client::show', $client->id))
            ->assertSuccessful()
            ->assertViewIs('client::show')
            ->assertSee('Test Client')
            ->assertSee('test@example.com');
    });

    it('renders client show view with invoices', function () {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
        ]);

        get(route('client::show', $client->id))
            ->assertSuccessful()
            ->assertSee('Factures')
            ->assertSee($invoice->invoice_number);
    });
});

describe('Invoice Views', function () {
    it('renders invoice index view', function () {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
        ]);

        get(route('invoice::index'))
            ->assertSuccessful()
            ->assertViewIs('invoice::index')
            ->assertSee('Factures')
            ->assertSee($invoice->invoice_number);
    });

    it('renders invoice create view with clients', function () {
        $client = Client::factory()->create();

        get(route('invoice::create'))
            ->assertSuccessful()
            ->assertViewIs('invoice::create')
            ->assertSee('Nouvelle facture')
            ->assertSee($client->name);
    });

    it('renders invoice edit view with clients', function () {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
        ]);

        get(route('invoice::edit', $invoice->id))
            ->assertSuccessful()
            ->assertViewIs('invoice::edit')
            ->assertSee('Modifier la facture');
    });

    it('renders invoice show view', function () {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
            'invoice_number' => 'INV-1234',
        ]);

        get(route('invoice::show', $invoice->id))
            ->assertSuccessful()
            ->assertViewIs('invoice::show')
            ->assertSee('INV-1234')
            ->assertSee($client->name);
    });

    it('renders invoice show view with items', function () {
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create([
            'client_id' => $client->id,
        ]);
        $item = InvoiceItem::factory()->create([
            'invoice_id' => $invoice->id,
            'description' => 'Test Item',
        ]);

        get(route('invoice::show', $invoice->id))
            ->assertSuccessful()
            ->assertSee('Test Item');
    });
});

describe('Product Views', function () {
    it('renders product index view', function () {
        $product = Product::factory()->create();

        get(route('product::index'))
            ->assertSuccessful()
            ->assertViewIs('product::index')
            ->assertSee('Produits')
            ->assertSee($product->name);
    });

    it('renders product create view', function () {
        get(route('product::create'))
            ->assertSuccessful()
            ->assertViewIs('product::create')
            ->assertSee('Nouveau produit');
    });

    it('renders product edit view', function () {
        $product = Product::factory()->create();

        get(route('product::edit', $product->id))
            ->assertSuccessful()
            ->assertViewIs('product::edit')
            ->assertSee('Modifier le produit')
            ->assertSee($product->name);
    });

    it('renders product show view', function () {
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 99.99,
        ]);

        get(route('product::show', $product->id))
            ->assertSuccessful()
            ->assertViewIs('product::show')
            ->assertSee('Test Product');
    });
});

describe('Component Views', function () {
    it('renders alert component', function () {
        $view = View::make('components.shared.alert', [
            'type' => 'success',
            'dismissible' => true,
        ])->with('slot', 'Test message');

        $html = $view->render();
        expect($html)->toContain('bg-green-50');
    });

    it('renders button component', function () {
        $view = View::make('components.shared.button', [
            'type' => 'submit',
            'variant' => 'primary',
        ])->with('slot', 'Click me');

        $html = $view->render();
        expect($html)->toContain('bg-blue-600');
        expect($html)->toContain('Click me');
    });

    it('renders card component', function () {
        $view = View::make('components.shared.card', [
            'title' => 'Test Card',
        ])->with('slot', 'Card content');

        $html = $view->render();
        expect($html)->toContain('Test Card');
        expect($html)->toContain('Card content');
    });

    it('renders input component', function () {
        $view = View::make('components.shared.input', [
            'name' => 'test',
            'label' => 'Test Label',
            'type' => 'text',
        ]);

        $html = $view->render();
        expect($html)->toContain('Test Label');
        expect($html)->toContain('name="test"');
    });

    it('renders invoice status component', function () {
        $view = View::make('components.invoice.invoice-status', [
            'status' => InvoiceStatus::PAID,
        ]);

        $html = $view->render();
        expect($html)->toContain('bg-green-100');
    });
});
