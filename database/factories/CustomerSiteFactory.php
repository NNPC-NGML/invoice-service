<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\Skillz\Nnpcreusable\Models\CustomerSite>
 */
class CustomerSiteFactory extends Factory
{
    protected $model = \Skillz\Nnpcreusable\Models\CustomerSite::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => fake()->randomNumber(),
            'site_address' => fake()->address(),
            'ngml_zone_id' => fake()->randomNumber(),
            'site_name' => fake()->company(),
            'phone_number' => fake()->phoneNumber(),
            'email' => fake()->email(),
            'site_contact_person_name' => fake()->name(),
            'site_contact_person_email' => fake()->email(),
            'site_contact_person_phone_number' => fake()->phoneNumber(),
            'site_contact_person_signature' => null,
            'site_existing_status' => fake()->boolean(),
            'created_by_user_id' => fake()->randomNumber(),
            'status' => fake()->boolean(),
        ];
    }
}
