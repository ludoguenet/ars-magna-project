<?php

declare(strict_types=1);

use App\Models\User;
use AppModules\Client\src\DataTransferObjects\ClientDTO;
use AppModules\Client\src\Models\Client;
use AppModules\Client\src\Services\ClientService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('can create a client using service', function () {
    $service = app(ClientService::class);

    $clientData = new ClientDTO(
        name: 'Test Client',
        email: 'test@example.com',
    );

    $client = $service->create($clientData);

    expect($client)
        ->toBeInstanceOf(Client::class)
        ->name->toBe('Test Client')
        ->email->toBe('test@example.com');

    $this->assertDatabaseHas('users', [
        'email' => 'test@example.com',
        'name' => 'Test Client',
    ]);

    $this->assertDatabaseHas('clients', [
        'user_id' => $client->user_id,
    ]);
});

it('can update a client using service', function () {
    $service = app(ClientService::class);

    $user = User::factory()->create(['name' => 'Original Name']);
    $client = Client::factory()->create(['user_id' => $user->id]);

    $clientData = new ClientDTO(
        name: 'Updated Name',
        phone: '9876543210',
    );

    $updatedClient = $service->update($client, $clientData);

    expect($updatedClient)
        ->name->toBe('Updated Name')
        ->phone->toBe('9876543210');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
    ]);

    $this->assertDatabaseHas('clients', [
        'id' => $client->id,
        'phone' => '9876543210',
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
    $user1 = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    $user2 = User::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);
    Client::factory()->create(['user_id' => $user1->id]);
    Client::factory()->create(['user_id' => $user2->id]);

    $client = Client::whereHas('user', function ($query) {
        $query->where('email', 'john@example.com');
    })->first();

    expect($client)
        ->not->toBeNull()
        ->name->toBe('John Doe');
});
