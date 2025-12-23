<?php

namespace AppModules\Invoice\database\factories;

use AppModules\Client\src\Models\Client;
use AppModules\Invoice\src\Enums\InvoiceStatus;
use AppModules\Invoice\src\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AppModules\Invoice\src\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomFloat(2, 100, 10000);
        $taxAmount = $subtotal * 0.21; // 21% VAT
        $total = $subtotal + $taxAmount;

        return [
            'invoice_number' => 'INV-'.fake()->unique()->numerify('####'),
            'client_id' => Client::factory(),
            'status' => InvoiceStatus::DRAFT,
            'issued_at' => fake()->dateTimeBetween('-1 year', 'now'),
            'due_at' => fake()->dateTimeBetween('now', '+30 days'),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => 0,
            'total' => $total,
            'notes' => fake()->optional()->sentence(),
            'terms' => fake()->optional()->paragraph(),
        ];
    }
}
