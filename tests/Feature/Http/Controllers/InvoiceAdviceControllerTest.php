<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\InvoiceAdvice;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceAdviceControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test it can list invoice advices with pagination.
     *
     * @return void
     */
    public function test_it_can_list_invoice_advices_with_pagination()
    {
        $this->actingAsAuthenticatedTestUser();

        // Create some dummy data
        InvoiceAdvice::factory()->count(15)->create(); // Create 15 records

        // Request the first page with a per_page limit of 10
        $response = $this->getJson('/api/invoice-advice?per_page=10');

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
        $response = $this->getJson('/api/invoice-advice?per_page=10&page=2');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(5, 'data'); // Should return 5 records on the second page
    }

    /**
     * Test if a specific invoice advice can be viewed by ID.
     *
     * @return void
     */
    public function test_it_can_view_a_single_invoice_advice()
    {
        $this->actingAsAuthenticatedTestUser();
        $invoiceAdvice = InvoiceAdvice::factory()->create();

        $response = $this->getJson("/api/invoice-advice/{$invoiceAdvice->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $invoiceAdvice->id,
                    'with_vat' => $invoiceAdvice->with_vat,
                    'customer_id' => $invoiceAdvice->customer_id,
                    'customer_site_id' => $invoiceAdvice->customer_site_id,
                    'capex_recovery_amount' => $invoiceAdvice->capex_recovery_amount,
                    'date' => $invoiceAdvice->date->toDateTimeString(),
                    'status' => $invoiceAdvice->status,
                    'created_at' => $invoiceAdvice->created_at->toDateTimeString(),
                    'updated_at' => $invoiceAdvice->updated_at->toDateTimeString(),
                ],
            ]);
    }

    /**
     * Test if an invoice advice can be deleted.
     *
     * @return void
     */
    public function test_it_can_delete_an_invoice_advice()
    {
        $this->actingAsAuthenticatedTestUser();
        $invoiceAdvice = InvoiceAdvice::factory()->create();

        $response = $this->deleteJson("/api/invoice-advice/{$invoiceAdvice->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('invoice_advice', [
            'id' => $invoiceAdvice->id,
        ]);
    }
}
