<?php

namespace App\Http\Middleware;

use Closure;

class ApiAuthMiddleware
{
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        return $next($request);
    }
}
