<?php

declare(strict_types=1);

namespace AppModules\Client\src\DataTransferObjects;

use AppModules\Client\src\Models\Client;
use Illuminate\Http\Request;

final readonly class ClientDTO
{
    public function __construct(
        public ?int $id = null,
        public string $name = '',
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $company = null,
        public ?string $vatNumber = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $postalCode = null,
        public ?string $country = null,
        public ?string $notes = null,
        public ?string $fullAddress = null,
    ) {}

    /**
     * Create from HTTP request.
     */
    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->string('name')->toString(),
            email: $request->filled('email') ? $request->string('email')->toString() : null,
            phone: $request->filled('phone') ? $request->string('phone')->toString() : null,
            company: $request->filled('company') ? $request->string('company')->toString() : null,
            vatNumber: $request->filled('vat_number') ? $request->string('vat_number')->toString() : null,
            address: $request->filled('address') ? $request->string('address')->toString() : null,
            city: $request->filled('city') ? $request->string('city')->toString() : null,
            postalCode: $request->filled('postal_code') ? $request->string('postal_code')->toString() : null,
            country: $request->filled('country') ? $request->string('country')->toString() : null,
            notes: $request->filled('notes') ? $request->string('notes')->toString() : null,
        );
    }

    /**
     * Create from validated array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'] ?? null,
            phone: $data['phone'] ?? null,
            company: $data['company'] ?? null,
            vatNumber: $data['vat_number'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            postalCode: $data['postal_code'] ?? null,
            country: $data['country'] ?? null,
            notes: $data['notes'] ?? null,
        );
    }

    /**
     * Create from Eloquent model.
     */
    public static function fromModel(Client $client): self
    {
        $client->loadMissing(['user', 'address']);

        return new self(
            id: $client->id,
            name: $client->name,
            email: $client->email,
            phone: $client->phone,
            company: $client->company,
            vatNumber: $client->vat_number,
            address: $client->address?->address,
            city: $client->address?->city,
            postalCode: $client->address?->postal_code,
            country: $client->address?->country,
            notes: $client->notes,
            fullAddress: $client->full_address,
        );
    }

    /**
     * Convert to array for Eloquent (client-specific fields only).
     * Note: name and email are stored in the User model, not the Client model.
     */
    public function toArray(): array
    {
        return array_filter([
            'phone' => $this->phone,
            'company' => $this->company,
            'vat_number' => $this->vatNumber,
            'notes' => $this->notes,
        ], fn ($value) => $value !== null);
    }

    /**
     * Get address data as array.
     */
    public function getAddressData(): ?array
    {
        if (! $this->address && ! $this->city && ! $this->postalCode && ! $this->country) {
            return null;
        }

        return array_filter([
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postalCode,
            'country' => $this->country,
        ], fn ($value) => $value !== null);
    }
}
