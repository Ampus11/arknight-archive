<?php

namespace Database\Factories;

use App\Models\Operator;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Operator>
 */
class OperatorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $class = fake()->randomElement(Operator::classes());

        return [
            'name' => 'Operator '.fake()->unique()->numberBetween(1, 1000000),
            'rarity' => fake()->numberBetween(3, 6),
            'class' => $class,
            'position' => in_array($class, ['Vanguard', 'Guard', 'Defender', 'Specialist'], true)
                ? 'Melee'
                : 'Ranged',
            'description' => fake()->sentence(),
            'accent' => fake()->hexColor(),
        ];
    }
}
