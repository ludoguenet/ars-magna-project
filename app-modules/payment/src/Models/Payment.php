<?php

namespace AppModules\Payment\src\Models;

use AppModules\Invoice\src\Models\Invoice;
use AppModules\Payment\database\factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $invoice_id
 * @property float $amount
 * @property string $status
 * @property string|null $payment_method
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Invoice $invoice
 */
class Payment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return PaymentFactory::new();
    }

    protected $fillable = [
        'invoice_id',
        'amount',
        'status',
        'payment_method',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the invoice.
     *
     * @internal This relationship is for internal use only within the Payment module.
     * Other modules should use InvoiceRepositoryContract to access invoice data.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(\AppModules\Invoice\src\Models\Invoice::class);
    }

    /**
     * Check if payment is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if payment is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
