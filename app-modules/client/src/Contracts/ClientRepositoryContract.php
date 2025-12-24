<?php

namespace AppModules\Client\src\Contracts;

use AppModules\Client\src\DataTransferObjects\ClientDTO;

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
}
