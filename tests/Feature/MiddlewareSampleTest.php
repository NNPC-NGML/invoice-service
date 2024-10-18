<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MiddlewareSampleTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_protected_route_allows_access_with_valid_scope(): void
    {
        $this->actingAsAuthenticatedTestUser();
        $response = $this->getJson('/api/protected');
        $response->assertStatus(200);
        $response->assertJson(['message' => 'Access granted']);
    }

    public function test_protected_route_denies_access_with_invalid_scope(): void
    {
        $this->actingAsUnAuthenticatedTestUser();
        $response = $this->getJson('/api/protected');
        $response->assertStatus(401);
    }
}