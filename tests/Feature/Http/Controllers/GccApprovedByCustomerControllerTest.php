<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\GccApprovedByCustomer; // Adjust the model namespace as needed
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GccApprovedByCustomerControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test it can list approved entries with pagination.
     *
     * @return void
     */
    public function test_it_can_list_approved_entries_with_pagination()
    {
        $this->actingAsAuthenticatedTestUser();

        // Create some dummy data
        GccApprovedByCustomer::factory()->count(15)->create(); // Create 15 records

        // Request the first page with a per_page limit of 10
        $response = $this->getJson('/api/gcc-approved-by-customers?per_page=10');

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
        $response = $this->getJson('/api/gcc-approved-by-customers?per_page=10&page=2');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(5, 'data'); // Should return 5 records on the second page
    }

    /**
     * Test if a specific approved entry can be viewed by ID.
     *
     * @return void
     */
    public function test_it_can_view_a_single_approved_entry()
    {
        $this->actingAsAuthenticatedTestUser();
        $approvedEntry = GccApprovedByCustomer::factory()->create();

        $response = $this->getJson("/api/gcc-approved-by-customers/{$approvedEntry->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $approvedEntry->id,
                    'customer_name' => $approvedEntry->customer_name,
                    'signature' => $approvedEntry->signature,
                    'date' => $approvedEntry->date->toDateTimeString(),
                    'created_at' => $approvedEntry->created_at->toDateTimeString(),
                    'updated_at' => $approvedEntry->updated_at->toDateTimeString(),
                ],
            ]);
    }

    /**
     * Test if an approved entry can be deleted.
     *
     * @return void
     */
    public function test_it_can_delete_an_approved_entry()
    {
        $this->actingAsAuthenticatedTestUser();
        $approvedEntry = GccApprovedByCustomer::factory()->create();

        $response = $this->deleteJson("/api/gcc-approved-by-customers/{$approvedEntry->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('gcc_approved_by_customers', [
            'id' => $approvedEntry->id,
        ]);
    }
}
