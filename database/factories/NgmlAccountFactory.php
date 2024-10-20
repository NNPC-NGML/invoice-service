<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NgmlAccount>
 */
class NgmlAccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bank_name' => $this->faker->company,
            'bank_address' => $this->faker->address,
            'account_name' => $this->faker->name,
            'account_number' => $this->faker->bankAccountNumber,
            'sort_code' => $this->faker->bankAccountNumber, // Adjust as necessary
            'tin' => $this->faker->buildingNumber,
        ];
    }
}
