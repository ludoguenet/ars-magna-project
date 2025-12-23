<?php

namespace AppModules\Client\src\Services;

use AppModules\Client\src\Models\Client;
use AppModules\Client\src\Repositories\ClientRepository;

class ClientService
{
    public function __construct(
        private ClientRepository $repository
    ) {}

    /**
     * Create a new client.
     */
    public function create(array $data): Client
    {
        return $this->repository->create($data);
    }

    /**
     * Update a client.
     */
    public function update(Client $client, array $data): Client
    {
        $this->repository->update($client, $data);

        return $client->fresh();
    }

    /**
     * Delete a client.
     */
    public function delete(Client $client): bool
    {
        return $this->repository->delete($client);
    }
}
