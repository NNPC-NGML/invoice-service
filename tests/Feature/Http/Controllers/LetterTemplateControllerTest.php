<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\LetterTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LetterTemplateControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test it can list letter templates with pagination.
     *
     * @return void
     */
    public function test_it_can_list_letter_templates_with_pagination()
    {
        $this->actingAsAuthenticatedTestUser();

        // Create some dummy data
        LetterTemplate::factory()->count(15)->create(); // Create 15 records

        // Request the first page with a per_page limit of 10
        $response = $this->getJson('/api/letter-templates?per_page=10');

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
        $response = $this->getJson('/api/letter-templates?per_page=10&page=2');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
            ])
            ->assertJsonCount(5, 'data'); // Should return 5 records on the second page
    }

    /**
     * Test if a specific letter template can be viewed by ID.
     *
     * @return void
     */
    public function test_it_can_view_a_single_letter_template()
    {
        $this->actingAsAuthenticatedTestUser();
        $letterTemplate = LetterTemplate::factory()->create();

        $response = $this->getJson("/api/letter-templates/{$letterTemplate->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'data' => [
                    'id' => $letterTemplate->id,
                    'letter' => $letterTemplate->letter,
                    'status' => $letterTemplate->status,
                    'created_at' => $letterTemplate->created_at->toDateTimeString(),
                    'updated_at' => $letterTemplate->updated_at->toDateTimeString(),
                ],
            ]);
    }

    /**
     * Test if a letter template can be deleted.
     *
     * @return void
     */
    public function test_it_can_delete_a_letter_template()
    {
        $this->actingAsAuthenticatedTestUser();
        $letterTemplate = LetterTemplate::factory()->create();

        $response = $this->deleteJson("/api/letter-templates/{$letterTemplate->id}");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('letter_templates', [
            'id' => $letterTemplate->id,
        ]);
    }
}
