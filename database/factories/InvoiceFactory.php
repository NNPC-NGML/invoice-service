<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Invoice;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_number' => $this->faker->unique()->numerify('INV-######'), // Generates a unique invoice number
            'invoice_advice_id' => $this->faker->numberBetween(1, 100), // Adjust range as necessary for your application
            'consumed_volume_amount_in_naira' => $this->faker->randomFloat(2, 1000, 1000000), // Random float for Naira
            'consumed_volume_amount_in_dollar' => $this->faker->randomFloat(2, 1000, 1000000), // Random float for Dollars
            'dollar_to_naira_convertion_rate' => $this->faker->randomFloat(4, 300, 600), // Example conversion rate range
            'vat_amount' => $this->faker->randomFloat(2, 0, 100000), // Random float for VAT amount
            'total_volume_paid_for' => $this->faker->randomFloat(2, 1000, 1000000), // Total amount paid
            'status' => $this->faker->numberBetween(0, 2), // Adjust range for invoice statuses if necessary
        ];
    }
}
