<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DailyVolume>
 */
class DailyVolumeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => fake()->numberBetween(1, 10000),
            'customer_site_id' => fake()->numberBetween(1, 10000),
            'volume' => fake()->randomFloat(2, 0, 1000000),
            'remark' => fake()->sentence(10),
        ];
    }
}
