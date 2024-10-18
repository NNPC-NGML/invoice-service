<?php

namespace App\Http\Middleware;

use Closure;
use Skillz\UserService;
use Illuminate\Http\Request;


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

        return $next($request);
    }
}

