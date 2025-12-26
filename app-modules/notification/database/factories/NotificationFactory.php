<?php

declare(strict_types=1);

namespace AppModules\Notification\database\factories;

use AppModules\Notification\src\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AppModules\Notification\src\Models\Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'title' => fake()->sentence(3),
            'type' => fake()->randomElement(['invoice_created', 'payment_received', 'invoice_overdue']),
            'message' => fake()->sentence(),
            'data' => [
                'link' => fake()->optional()->url(),
            ],
            'read_at' => null,
        ];
    }
}
