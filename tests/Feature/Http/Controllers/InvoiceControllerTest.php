<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Invoice; // Ensure you have the Invoice model created
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test it can list invoices with pagination.
     *
     * @return void
     */
    public function test_it_can_list_invoices_with_pagination()
    {
        $this->actingAsAuthenticatedTestUser();

        // Create some dummy data
        Invoice::factory()->count(15)->create(); // Create 15 records

        // Request the first page with a per_page limit of 10
        $response = $this->getJson('/api/invoices?per_page=10');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(10, 'data'); // Should return 10 records on the first page

        // Check for pagination metadata
        $this->assertArrayHasKey('current_page', $response->json('meta'));
        $this->assertArrayHasKey('last_page', $response->json('meta'));
        $this->assertArrayHasKey('per_page', $response->json('meta'));
        $this->assertArrayHasKey('total', $response->json('meta'));

        // Now request the second page
        $response = $this->getJson('/api/invoices?per_page=10&page=2');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(5, 'data'); // Should return 5 records on the second page
    }

    /**
     * Test if a specific invoice can be viewed by ID.
     *
     * @return void
     */
    public function test_it_can_view_a_single_invoice()
    {
        $this->actingAsAuthenticatedTestUser();
        $invoice = Invoice::factory()->create();

        $response = $this->getJson("/api/invoices/{$invoice->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_advice_id' => $invoice->invoice_advice_id,
                    'consumed_volume_amount_in_naira' => $invoice->consumed_volume_amount_in_naira,
                    'consumed_volume_amount_in_dollar' => $invoice->consumed_volume_amount_in_dollar,
                    'dollar_to_naira_convertion_rate' => $invoice->dollar_to_naira_convertion_rate,
                    'vat_amount' => $invoice->vat_amount,
                    'total_volume_paid_for' => $invoice->total_volume_paid_for,
                    'status' => $invoice->status,
                    'created_at' => $invoice->created_at->toDateTimeString(),
                    'updated_at' => $invoice->updated_at->toDateTimeString(),
                ],
            ]);
    }

    /**
     * Test if an invoice can be deleted.
     *
     * @return void
     */
    public function test_it_can_delete_an_invoice()
    {
        $this->actingAsAuthenticatedTestUser();
        $invoice = Invoice::factory()->create();

        $response = $this->deleteJson("/api/invoices/{$invoice->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('invoices', [
            'id' => $invoice->id,
        ]);
    }
}
