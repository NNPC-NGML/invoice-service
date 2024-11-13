<?php

namespace Tests;

use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    public function actingAsAuthenticatedTestUser()
    {
        // Create or retrieve a user instance
        $user = \App\Models\User::factory()->create([
            'id' => 1,  // Ensure it matches the ID in the fake response, if necessary
        ]);

        // Mock HTTP responses
        Http::fake([
            env("USERS_MS") . '/*' => Http::response(["id" => $user->id], 200),
        ]);

        // Set the user as the authenticated user
        $this->actingAs($user);

        return $user;

    }
    public function actingAsUnAuthenticatedTestUser()
    {
        Http::fake([
            env("USERS_MS") . '/*' => Http::response('unauthorized', 401),
        ]);

    }
}
