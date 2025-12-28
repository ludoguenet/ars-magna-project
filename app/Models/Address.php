<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property string $addressable_type
 * @property int $addressable_id
 * @property string|null $address
 * @property string|null $city
 * @property string|null $postal_code
 * @property string|null $country
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Model $addressable
 */
class Address extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return AddressFactory::new();
    }

    /**
     * Get the parent addressable model.
     */
    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the full address.
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->postal_code,
            $this->city,
            $this->country,
        ]);

        return implode(', ', $parts);
    }
}
