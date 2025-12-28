<?php

declare(strict_types=1);

namespace AppModules\Client\database\factories;

use App\Models\Address;
use App\Models\User;
use AppModules\Client\src\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AppModules\Client\src\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'phone' => fake()->phoneNumber(),
            'company' => fake()->company(),
            'vat_number' => fake()->numerify('BE#########'),
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (Client $client) {
            // Create address for the client
            Address::factory()->create([
                'addressable_type' => Client::class,
                'addressable_id' => $client->id,
            ]);
        });
    }
}
