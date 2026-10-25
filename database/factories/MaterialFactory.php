<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Material>
 */
class MaterialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Material '.fake()->unique()->numberBetween(1, 1000000),
            'tier' => fake()->numberBetween(1, 5),
            'emoji' => '🧪',
            'color' => fake()->hexColor(),
        ];
    }
}
