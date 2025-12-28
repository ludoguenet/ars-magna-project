<?php

declare(strict_types=1);

namespace AppModules\Client\src\Repositories;

use AppModules\Client\src\Contracts\ClientRepositoryContract;
use AppModules\Client\src\DataTransferObjects\ClientDTO;
use AppModules\Client\src\Models\Client;

class ClientRepository implements ClientRepositoryContract
{
    /**
     * Get all clients.
     *
     * @return array<ClientDTO>
     */
    public function all(): array
    {
        return Client::query()
            ->with(['user', 'address'])
            ->join('users', 'clients.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('clients.*')
            ->get()
            ->map(fn (Client $client) => ClientDTO::fromModel($client))
            ->toArray();
    }

    /**
     * Find a client by ID.
     */
    public function find(int $id): ?ClientDTO
    {
        $client = Client::with(['user', 'address'])->find($id);

        return $client ? ClientDTO::fromModel($client) : null;
    }

    /**
     * Search clients by name, email, or company.
     *
     * @return array<ClientDTO>
     */
    public function search(string $query): array
    {
        return Client::query()
            ->with(['user', 'address'])
            ->join('users', 'clients.user_id', '=', 'users.id')
            ->where(function ($q) use ($query) {
                $q->where('users.name', 'like', "%{$query}%")
                    ->orWhere('users.email', 'like', "%{$query}%")
                    ->orWhere('clients.company', 'like', "%{$query}%");
            })
            ->orderBy('users.name')
            ->select('clients.*')
            ->get()
            ->map(fn (Client $client) => ClientDTO::fromModel($client))
            ->toArray();
    }

    /**
     * Create a new client.
     */
    public function create(ClientDTO $data): Client
    {
        return Client::create($data->toArray());
    }

    /**
     * Create a new client from array (internal use only).
     */
    public function createFromArray(array $data): Client
    {
        return Client::create($data);
    }

    /**
     * Update a client.
     */
    public function update(Client $client, ClientDTO $data): bool
    {
        return $client->update($data->toArray());
    }

    /**
     * Delete a client.
     */
    public function delete(Client $client): bool
    {
        return $client->delete();
    }

    /**
     * Find a client model by ID (internal use only).
     */
    public function findModel(int $id): ?Client
    {
        return Client::with(['user', 'address'])->find($id);
    }

    /**
     * Get all client models (internal use only).
     */
    public function allModels(): \Illuminate\Database\Eloquent\Collection
    {
        return Client::query()
            ->with(['user', 'address'])
            ->join('users', 'clients.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('clients.*')
            ->get();
    }
}
