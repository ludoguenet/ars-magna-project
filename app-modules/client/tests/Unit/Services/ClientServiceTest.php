<?php

declare(strict_types=1);

use AppModules\Client\src\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can create a client using service', function () {
    $client = Client::factory()->create([
        'name' => 'Test Client',
        'email' => 'test@example.com',
    ]);

    expect($client)
        ->toBeInstanceOf(Client::class)
        ->name->toBe('Test Client')
        ->email->toBe('test@example.com');

    $this->assertDatabaseHas('clients', [
        'email' => 'test@example.com',
        'name' => 'Test Client',
    ]);
});

it('can update a client using service', function () {
    $client = Client::factory()->create(['name' => 'Original Name']);

    $client->update([
        'name' => 'Updated Name',
        'phone' => '9876543210',
    ]);

    expect($client->refresh())
        ->name->toBe('Updated Name')
        ->phone->toBe('9876543210');

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'name' => 'Updated Name',
    ]);
});

it('can delete a client using service', function () {
    $client = Client::factory()->create();

    $client->delete();

    $this->assertSoftDeleted('clients', [
        'id' => $client->id,
    ]);
});

it('can retrieve all clients', function () {
    Client::factory()->count(3)->create();

    $clients = Client::all();

    expect($clients)->toHaveCount(3);
});

it('can find a client by email', function () {
    Client::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    Client::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

    $client = Client::where('email', 'john@example.com')->first();

    expect($client)
        ->not->toBeNull()
        ->name->toBe('John Doe');
});
