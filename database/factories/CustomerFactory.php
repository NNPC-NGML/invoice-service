<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Skillz\Nnpcreusable\Models\Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = \Skillz\Nnpcreusable\Models\Customer::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_name' => fake()->company(),
            'email' => fake()->email(),
            'phone_number' => fake()->phoneNumber(),
            'password' => fake()->password(),
            'created_by_user_id' => 1,
            'status' => fake()->boolean(),
        ];
    }
}
