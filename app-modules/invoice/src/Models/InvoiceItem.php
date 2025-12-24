<?php

namespace AppModules\Invoice\src\Models;

use AppModules\Invoice\database\factories\InvoiceItemFactory;
use AppModules\Shared\src\ValueObjects\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $invoice_id
 * @property int|null $product_id
 * @property string $description
 * @property float $quantity
 * @property float $unit_price
 * @property float $tax_rate
 * @property float $discount_amount
 * @property float $line_total
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Invoice $invoice
 * @property-read \AppModules\Product\src\Models\Product|null $product
 */
class InvoiceItem extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return InvoiceItemFactory::new();
    }

    protected $fillable = [
        'invoice_id',
        'product_id',
        'description',
        'quantity',
        'unit_price',
        'tax_rate',
        'discount_amount',
        'line_total',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    /**
     * Get the invoice.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the product.
     *
     * @internal This relationship is for internal use only within the Invoice module.
     * Other modules should use ProductRepositoryContract to access product data.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(\AppModules\Product\src\Models\Product::class);
    }

    /**
     * Calculate the line total.
     */
    public function calculateLineTotal(): Money
    {
        $subtotal = Money::fromDecimal((float) $this->unit_price)
            ->multiply((float) $this->quantity);

        if ($this->discount_amount > 0) {
            $subtotal = $subtotal->subtract(
                Money::fromDecimal((float) $this->discount_amount)
            );
        }

        if ($this->tax_rate > 0) {
            $taxAmount = $subtotal->multiply((float) $this->tax_rate / 100);

            return $subtotal->add($taxAmount);
        }

        return $subtotal;
    }
}
