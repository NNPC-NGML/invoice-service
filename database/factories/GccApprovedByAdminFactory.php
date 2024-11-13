<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GccApprovedByAdmin>
 */
class GccApprovedByAdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => fake()->randomElement([1, 2, 3, 4, 5]), // Adjust this as needed for your user IDs
            'invoice_advice_id' => fake()->randomElement([1, 2, 3, 4, 5]), // Adjust this for valid invoice advice IDs
            'date' => fake()->dateTime(),
        ];
    }
}
