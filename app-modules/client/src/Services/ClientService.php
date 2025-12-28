<?php

declare(strict_types=1);

namespace AppModules\Client\src\Services;

use App\Models\Address;
use App\Models\User;
use AppModules\Client\src\Contracts\ClientRepositoryContract;
use AppModules\Client\src\DataTransferObjects\ClientDTO;
use AppModules\Client\src\Models\Client;

class ClientService
{
    public function __construct(
        private ClientRepositoryContract $repository
    ) {}

    /**
     * Create a new client.
     */
    public function create(ClientDTO $data): Client
    {
        // Create the user
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email ?? fake()->unique()->safeEmail(),
            'password' => bcrypt('password'),
        ]);

        // Create the client with user_id
        $clientData = $data->toArray();
        $clientData['user_id'] = $user->id;

        $client = $this->repository->createFromArray($clientData);

        // Create address if provided
        $addressData = $data->getAddressData();
        if ($addressData !== null) {
            $client->address()->create($addressData);
        }

        return $client->fresh(['user', 'address']);
    }

    /**
     * Update a client.
     */
    public function update(Client $client, ClientDTO $data): Client
    {
        // Load the user relationship
        $client->load('user', 'address');

        // Update user's name and email if provided
        if ($client->user) {
            $userUpdates = [];
            if ($data->name !== $client->user->name) {
                $userUpdates['name'] = $data->name;
            }
            if ($data->email !== null && $data->email !== $client->user->email) {
                $userUpdates['email'] = $data->email;
            }
            if (! empty($userUpdates)) {
                $client->user->update($userUpdates);
            }
        }

        // Update client-specific fields
        $this->repository->update($client, $data);

        // Update or create address
        $addressData = $data->getAddressData();
        if ($addressData !== null) {
            if ($client->address) {
                $client->address->update($addressData);
            } else {
                $client->address()->create($addressData);
            }
        } elseif ($client->address) {
            // If no address data provided but address exists, delete it
            $client->address->delete();
        }

        return $client->fresh(['user', 'address']);
    }

    /**
     * Delete a client.
     */
    public function delete(Client $client): bool
    {
        return $this->repository->delete($client);
    }
}
