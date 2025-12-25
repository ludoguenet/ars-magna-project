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

        return [
            'invoice_id' => Invoice::factory(),
            'product_id' => Product::factory(),
            'description' => fake()->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'tax_rate' => fake()->randomFloat(2, 0, 25),
            'discount_amount' => function (array $attributes) {
                $subtotal = $attributes['unit_price'] * $attributes['quantity'];

                return fake()->optional(0.3, 0)->randomFloat(2, 0, $subtotal * 0.2);
            },
            'line_total' => function (array $attributes) {
                $subtotal = ($attributes['unit_price'] * $attributes['quantity']) - $attributes['discount_amount'];

                return $subtotal + ($subtotal * $attributes['tax_rate'] / 100);
            },
        ];
    }
}
