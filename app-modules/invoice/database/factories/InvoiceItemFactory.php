<?php

declare(strict_types=1);

namespace AppModules\Invoice\database\factories;

use AppModules\Invoice\src\Models\Invoice;
use AppModules\Invoice\src\Models\InvoiceItem;
use AppModules\Product\src\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AppModules\Invoice\src\Models\InvoiceItem>
 */
class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $unitPrice = fake()->randomFloat(2, 10, 1000);
        $quantity = fake()->randomFloat(2, 1, 10);
        $discountAmount = fake()->optional(0.3, 0)->randomFloat(2, 0, $unitPrice * $quantity * 0.2);
        $taxRate = fake()->randomFloat(2, 0, 25);
        $subtotal = ($unitPrice * $quantity) - $discountAmount;
        $lineTotal = $subtotal + ($subtotal * $taxRate / 100);

        return [
            'invoice_id' => Invoice::factory(),
            'product_id' => Product::factory(),
            'description' => fake()->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_rate' => $taxRate,
            'discount_amount' => $discountAmount,
            'line_total' => $lineTotal,
        ];
    }
}
