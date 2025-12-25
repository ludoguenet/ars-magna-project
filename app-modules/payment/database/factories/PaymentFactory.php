<?php

declare(strict_types=1);

namespace AppModules\Payment\database\factories;

use AppModules\Invoice\src\Models\Invoice;
use AppModules\Payment\src\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AppModules\Payment\src\Models\Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'amount' => fake()->randomFloat(2, 10, 10000),
            'status' => fake()->randomElement(['pending', 'completed', 'failed', 'cancelled']),
            'payment_method' => fake()->optional()->randomElement(['credit_card', 'bank_transfer', 'paypal', 'stripe']),
            'notes' => fake()->optional()->sentence(),
            'paid_at' => fake()->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    /**
     * Indicate that the payment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'paid_at' => null,
        ]);
    }

    /**
     * Indicate that the payment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'paid_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}
