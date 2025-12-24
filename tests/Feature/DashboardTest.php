<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('can access the dashboard', function () {
    actingAs(User::factory()->create());
    get(route('dashboard'))
        ->assertSuccessful()
        ->assertViewIs('dashboard::index');
});

it('displays dashboard statistics', function () {
    actingAs(User::factory()->create());
    $response = get(route('dashboard'))
        ->assertSuccessful();

    $response->assertSee('Dashboard');
    $response->assertSee('Total Clients');
    $response->assertSee('Total Products');
    $response->assertSee('Total Invoices');
    $response->assertSee('Total Revenue');
    $response->assertSee('Pending Invoices');
    $response->assertSee('Overdue Invoices');
});

it('displays recent invoices', function () {
    actingAs(User::factory()->create());
    $response = get(route('dashboard'))
        ->assertSuccessful();

    $response->assertSee('Recent Invoices');
});
