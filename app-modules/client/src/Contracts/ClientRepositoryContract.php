<?php

declare(strict_types=1);

namespace AppModules\Client\src\Contracts;

use AppModules\Client\src\DataTransferObjects\ClientDTO;
use AppModules\Client\src\Models\Client;

interface ClientRepositoryContract
{
    /**
     * Get all clients.
     *
     * @return array<ClientDTO>
     */
    public function all(): array;

    /**
     * Find a client by ID.
     */
    public function find(int $id): ?ClientDTO;

    /**
     * Search clients by name, email, or company.
     *
     * @return array<ClientDTO>
     */
    public function search(string $query): array;

    /**
     * Create a new client.
     */
    public function create(ClientDTO $data): Client;

    /**
     * Create a new client from array (internal use only).
     */
    public function createFromArray(array $data): Client;

    /**
     * Update a client.
     */
    public function update(Client $client, ClientDTO $data): bool;

    /**
     * Delete a client.
     */
    public function delete(Client $client): bool;

    /**
     * Find a client model by ID (internal use only).
     */
    public function findModel(int $id): ?Client;

    /**
     * Get all client models (internal use only).
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, Client>
     */
    public function allModels(): \Illuminate\Database\Eloquent\Collection;
}
