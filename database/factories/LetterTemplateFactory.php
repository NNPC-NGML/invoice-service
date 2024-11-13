<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LetterTemplate>
 */
class LetterTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'letter' => fake()->paragraph(), // You can adjust this to use a different fake method, like sentence or text
            'status' => fake()->numberBetween(0, 1), // Assuming status is binary; adjust the range as needed
        ];
    }
}
