<?php

declare(strict_types=1);

namespace AppModules\Product\database\factories;

use AppModules\Product\src\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\AppModules\Product\src\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->optional()->paragraph(),
            'sku' => fake()->unique()->bothify('SKU-####-???'),
            'price' => fake()->randomFloat(2, 10, 1000),
            'tax_rate' => fake()->randomFloat(2, 0, 25),
            'unit' => fake()->randomElement(['piece', 'hour', 'day', 'month', 'kg', 'm']),
            'is_active' => true,
        ];
    }
}
