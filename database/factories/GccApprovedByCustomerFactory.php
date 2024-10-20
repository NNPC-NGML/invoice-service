<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GccApprovedByCustomer>
 */
class GccApprovedByCustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(), // Generating a random name for the customer
            'signature' => fake()->md5(), // Generating a random string as a signature
            'date' => fake()->dateTime(), // Generating a random date and time
        ];
    }
}
