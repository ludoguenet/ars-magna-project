<?php

declare(strict_types=1);

namespace AppModules\Client\src\DataTransferObjects;

use AppModules\Client\src\Models\Client;

final readonly class ClientDTO
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $company = null,
        public ?string $vatNumber = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $postalCode = null,
        public ?string $country = null,
        public ?string $notes = null,
    ) {}

    /**
     * Create from Eloquent model.
     */
    public static function fromModel(Client $client): self
    {
        return new self(
            id: $client->id,
            name: $client->name,
            email: $client->email,
            phone: $client->phone,
            company: $client->company,
            vatNumber: $client->vat_number,
            address: $client->address,
            city: $client->city,
            postalCode: $client->postal_code,
            country: $client->country,
            notes: $client->notes,
        );
    }
}
