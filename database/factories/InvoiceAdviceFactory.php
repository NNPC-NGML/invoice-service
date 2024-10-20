<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceAdvice>
 */
class InvoiceAdviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'with_vat' => fake()->boolean(),
            'customer_id' => fake()->randomNumber(),
            'customer_site_id' => fake()->randomNumber(),
            'capex_recovery_amount' => fake()->randomFloat(2, 0, 1000000),
            'date' => fake()->dateTime(),
            'status' => fake()->numberBetween(0, 3),
        ];
    }
}
