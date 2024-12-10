<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvoiceAdviceListItem>
 */
class InvoiceAdviceListItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => fake()->randomNumber(), // Adjust this to match an existing customer ID if necessary
            'customer_site_id' => fake()->randomNumber(), // Adjust this to match an existing customer site ID if necessary
            'invoice_advice_id' => fake()->randomNumber(), // Adjust this to match an existing invoice advice ID if necessary
            'daily_volume_id' => fake()->randomNumber(), // Adjust this to match an existing daily volume ID if necessary
            'volume' => fake()->randomFloat(2, 0, 1000000), // You can also use fake()->randomNumber() if volume is an integer
            'inlet' => fake()->word(), // Use word for random short strings, or customize as needed
            'outlet' => fake()->word(), // Similar to inlet
            'take_or_pay_value' => fake()->randomFloat(2, 0, 1000000),
            'allocation' => fake()->randomFloat(2, 0, 1000000),
            'daily_target' => fake()->randomFloat(2, 0, 1000000),
            'nomination' => fake()->randomFloat(2, 0, 1000000),
            'original_date' => fake()->dateTime(), // Use dateTime for timestamps
            'status' => fake()->numberBetween(0, 3), // Adjust this based on your status values
        ];
    }
}
