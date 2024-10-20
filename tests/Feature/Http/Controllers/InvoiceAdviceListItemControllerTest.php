<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\InvoiceAdviceListItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceAdviceListItemControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test it can list invoice advice list items with pagination.
     *
     * @return void
     */
    public function test_it_can_list_invoice_advice_list_items_with_pagination()
    {
        $this->actingAsAuthenticatedTestUser();

        // Create some dummy data
        InvoiceAdviceListItem::factory()->count(15)->create(); // Create 15 records

        // Request the first page with a per_page limit of 10
        $response = $this->getJson('/api/invoice-advice-list-items?per_page=10');

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
        $response = $this->getJson('/api/invoice-advice-list-items?per_page=10&page=2');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(5, 'data'); // Should return 5 records on the second page
    }

    /**
     * Test if a specific invoice advice list item can be viewed by ID.
     *
     * @return void
     */
    public function test_it_can_view_a_single_invoice_advice_list_item()
    {
        $this->actingAsAuthenticatedTestUser();
        $invoiceAdviceListItem = InvoiceAdviceListItem::factory()->create();

        $response = $this->getJson("/api/invoice-advice-list-items/{$invoiceAdviceListItem->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $invoiceAdviceListItem->id,
                    'customer_id' => $invoiceAdviceListItem->customer_id,
                    'customer_site_id' => $invoiceAdviceListItem->customer_site_id,
                    'volume' => $invoiceAdviceListItem->volume,
                    'inlet' => $invoiceAdviceListItem->inlet,
                    'outlet' => $invoiceAdviceListItem->outlet,
                    'take_or_pay_value' => $invoiceAdviceListItem->take_or_pay_value,
                    'allocation' => $invoiceAdviceListItem->allocation,
                    'daily_target' => $invoiceAdviceListItem->daily_target,
                    'nomination' => $invoiceAdviceListItem->nomination,
                    'daily_gas_id' => $invoiceAdviceListItem->daily_gas_id,
                    'date' => $invoiceAdviceListItem->date->toDateTimeString(),
                    'status' => $invoiceAdviceListItem->status,
                    'created_at' => $invoiceAdviceListItem->created_at->toDateTimeString(),
                    'updated_at' => $invoiceAdviceListItem->updated_at->toDateTimeString(),
                ],
            ]);
    }

    /**
     * Test if an invoice advice list item can be deleted.
     *
     * @return void
     */
    public function test_it_can_delete_an_invoice_advice_list_item()
    {
        $this->actingAsAuthenticatedTestUser();
        $invoiceAdviceListItem = InvoiceAdviceListItem::factory()->create();

        $response = $this->deleteJson("/api/invoice-advice-list-items/{$invoiceAdviceListItem->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('invoice_advice_list_items', [
            'id' => $invoiceAdviceListItem->id,
        ]);
    }
}
