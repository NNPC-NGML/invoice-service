<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Log;
use Closure;
use Skillz\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UsersMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = (new UserService)->getRequest('get', 'scope/user');

        if (!$response->ok()) {
            abort(401, 'unauthorized');
        }
        $userData = $response->json();

        // Create a user instance with the returned data
        $user = new \App\Models\User($userData);

        // Set the user in Auth
        Auth::setUser($user);

        // Add this line to bind the user to the request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }
}
