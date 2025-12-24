<?php

namespace AppModules\Invoice\src\Models;

use AppModules\Invoice\database\factories\InvoiceFactory;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Shared\src\ValueObjects\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $invoice_number
 * @property int $client_id
 * @property InvoiceStatus $status
 * @property \Illuminate\Support\Carbon|null $issued_at
 * @property \Illuminate\Support\Carbon|null $due_at
 * @property float $subtotal
 * @property float $tax_amount
 * @property float $discount_amount
 * @property float $total
 * @property string|null $notes
 * @property string|null $terms
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, InvoiceItem> $items
 * @property-read \AppModules\Client\src\Models\Client|null $client
 */
class Invoice extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return InvoiceFactory::new();
    }

    protected $fillable = [
        'invoice_number',
        'client_id',
        'status',
        'issued_at',
        'due_at',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'notes',
        'terms',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'due_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'status' => InvoiceStatus::class,
    ];

    /**
     * Get the invoice items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the client.
     *
     * @internal This relationship is for internal use only within the Invoice module.
     * Other modules should use ClientRepositoryContract to access client data.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(\AppModules\Client\src\Models\Client::class);
    }

    /**
     * Check if invoice is overdue.
     */
    public function isOverdue(): bool
    {
        return $this->status === InvoiceStatus::SENT
            && $this->due_at !== null
            && $this->due_at->isPast();
    }

    /**
     * Get the total as Money value object.
     */
    public function totalAsMoney(): Money
    {
        return Money::fromDecimal((float) $this->total);
    }

    /**
     * Scope for draft invoices.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', InvoiceStatus::DRAFT);
    }

    /**
     * Scope for paid invoices.
     */
    public function scopePaid($query)
    {
        return $query->where('status', InvoiceStatus::PAID);
    }

    /**
     * Scope for overdue invoices.
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', InvoiceStatus::SENT)
            ->where('due_at', '<', now());
    }
}
