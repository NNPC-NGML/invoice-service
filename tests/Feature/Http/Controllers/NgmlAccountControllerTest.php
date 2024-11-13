<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\NgmlAccount;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NgmlAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test it can list ngml accounts with pagination.
     *
     * @return void
     */
    public function test_it_can_list_ngml_accounts_with_pagination()
    {
        $this->actingAsAuthenticatedTestUser();

        // Create some dummy data
        NgmlAccount::factory()->count(15)->create(); // Create 15 records

        // Request the first page with a per_page limit of 10
        $response = $this->getJson('/api/ngml-accounts?per_page=10');

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
        $response = $this->getJson('/api/ngml-accounts?per_page=10&page=2');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(5, 'data'); // Should return 5 records on the second page
    }

    /**
     * Test if a specific ngml account can be viewed by ID.
     *
     * @return void
     */
    public function test_it_can_view_a_single_ngml_account()
    {
        $this->actingAsAuthenticatedTestUser();
        $ngmlAccount = NgmlAccount::factory()->create();

        $response = $this->getJson("/api/ngml-accounts/{$ngmlAccount->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $ngmlAccount->id,
                    'bank_name' => $ngmlAccount->bank_name,
                    'bank_address' => $ngmlAccount->bank_address,
                    'account_name' => $ngmlAccount->account_name,
                    'account_number' => $ngmlAccount->account_number,
                    'sort_code' => $ngmlAccount->sort_code,
                    'tin' => $ngmlAccount->tin,
                    'created_at' => $ngmlAccount->created_at->toDateTimeString(),
                    'updated_at' => $ngmlAccount->updated_at->toDateTimeString(),
                ],
            ]);
    }

    /**
     * Test if a ngml account can be deleted.
     *
     * @return void
     */
    public function test_it_can_delete_a_ngml_account()
    {
        $this->actingAsAuthenticatedTestUser();
        $ngmlAccount = NgmlAccount::factory()->create();

        $response = $this->deleteJson("/api/ngml-accounts/{$ngmlAccount->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('ngml_accounts', [
            'id' => $ngmlAccount->id,
        ]);
    }
}
