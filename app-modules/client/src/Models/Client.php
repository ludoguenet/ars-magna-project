<?php

declare(strict_types=1);

namespace AppModules\Client\src\Models;

use App\Models\Address;
use App\Models\User;
use AppModules\Client\database\factories\ClientFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $phone
 * @property string|null $company
 * @property string|null $vat_number
 * @property string|null $notes
 * @property-read Address|null $address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read User|null $user
 * @property-read string $name
 * @property-read string|null $email
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \AppModules\Invoice\src\Models\Invoice> $invoices
 */
class Client extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return ClientFactory::new();
    }

    /**
     * Get the user that owns the client.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client's name from the user.
     */
    public function getNameAttribute(): string
    {
        return $this->user->name;
    }

    /**
     * Get the client's email from the user.
     */
    public function getEmailAttribute(): ?string
    {
        return $this->user->email;
    }

    /**
     * Get the invoices for the client.
     *
     * @internal This relationship is for internal use only within the Client module.
     * Other modules should use InvoiceRepositoryContract to access invoice data.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(\AppModules\Invoice\src\Models\Invoice::class);
    }

    /**
     * Get the client's address.
     */
    public function address(): MorphOne
    {
        return $this->morphOne(Address::class, 'addressable');
    }

    /**
     * Get the full address.
     */
    public function getFullAddressAttribute(): ?string
    {
        return $this->address?->full_address;
    }
}
