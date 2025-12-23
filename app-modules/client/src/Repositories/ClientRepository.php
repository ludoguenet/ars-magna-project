<?php

namespace AppModules\Client\src\Repositories;

use AppModules\Client\src\Models\Client;
use Illuminate\Database\Eloquent\Collection;

class ClientRepository
{
    /**
     * Get all clients.
     */
    public function all(): Collection
    {
        return Client::query()->orderBy('name')->get();
    }

    /**
     * Find a client by ID.
     */
    public function find(int $id): ?Client
    {
        return Client::find($id);
    }

    /**
     * Create a new client.
     */
    public function create(array $data): Client
    {
        return Client::create($data);
    }

    /**
     * Update a client.
     */
    public function update(Client $client, array $data): bool
    {
        return $client->update($data);
    }

    /**
     * Delete a client.
     */
    public function delete(Client $client): bool
    {
        return $client->delete();
    }

    /**
     * Search clients by name or email.
     */
    public function search(string $query): Collection
    {
        return Client::where('name', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->orWhere('company', 'like', "%{$query}%")
            ->orderBy('name')
            ->get();
    }
}
