<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceAdviceApprovedBy>
 */
class InvoiceAdviceApprovedByFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->randomNumber(), // Assuming user_id is generated from another table
            'invoice_advice_id' => fake()->randomNumber(), // Assuming invoice_advice_id is generated from another table
            'approval_for' => fake()->numberBetween(1, 5), // Adjust range as necessary
            'date' => fake()->dateTime(), // Generates a random date and time
        ];
    }
}
