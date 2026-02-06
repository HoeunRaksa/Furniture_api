<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BankAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('X-Bank-Token');

        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Bank authentication token required.',
            ], 401);
        }

        $account = \App\Models\BankAccount::where('api_token', $token)
            ->where('is_active', true)
            ->first();

        if (!$account) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired bank authentication token.',
            ], 401);
        }

        // Attach account to request
        $request->merge(['bank_account' => $account]);

        return $next($request);
    }
}
