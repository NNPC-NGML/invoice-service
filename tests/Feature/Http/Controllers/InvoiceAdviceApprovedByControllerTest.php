<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\InvoiceAdviceApprovedBy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceAdviceApprovedByControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test it can list invoice advice approved records with pagination.
     *
     * @return void
     */
    public function test_it_can_list_invoice_advice_approved_bies_with_pagination()
    {
        $this->actingAsAuthenticatedTestUser();

        // Create some dummy data
        InvoiceAdviceApprovedBy::factory()->count(15)->create(); // Create 15 records

        // Request the first page with a per_page limit of 10
        $response = $this->getJson('/api/invoice-advice-approved-bies?per_page=10');

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
        $response = $this->getJson('/api/invoice-advice-approved-bies?per_page=10&page=2');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(5, 'data'); // Should return 5 records on the second page
    }

    /**
     * Test if a specific invoice advice approved record can be viewed by ID.
     *
     * @return void
     */
    public function test_it_can_view_a_single_invoice_advice_approved_by()
    {
        $this->actingAsAuthenticatedTestUser();
        $invoiceAdviceApprovedBy = InvoiceAdviceApprovedBy::factory()->create();

        $response = $this->getJson("/api/invoice-advice-approved-bies/{$invoiceAdviceApprovedBy->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $invoiceAdviceApprovedBy->id,
                    'user_id' => $invoiceAdviceApprovedBy->user_id,
                    'invoice_advice_id' => $invoiceAdviceApprovedBy->invoice_advice_id,
                    'approval_for' => $invoiceAdviceApprovedBy->approval_for,
                    'date' => $invoiceAdviceApprovedBy->date->toDateTimeString(),
                    'created_at' => $invoiceAdviceApprovedBy->created_at->toDateTimeString(),
                    'updated_at' => $invoiceAdviceApprovedBy->updated_at->toDateTimeString(),
                ],
            ]);
    }

    /**
     * Test if an invoice advice approved record can be deleted.
     *
     * @return void
     */
    public function test_it_can_delete_an_invoice_advice_approved_by()
    {
        $this->actingAsAuthenticatedTestUser();
        $invoiceAdviceApprovedBy = InvoiceAdviceApprovedBy::factory()->create();

        $response = $this->deleteJson("/api/invoice-advice-approved-bies/{$invoiceAdviceApprovedBy->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('invoice_advice_approved_bies', [
            'id' => $invoiceAdviceApprovedBy->id,
        ]);
    }
}
