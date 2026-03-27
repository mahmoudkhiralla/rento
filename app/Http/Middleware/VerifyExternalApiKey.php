<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyExternalApiKey
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $providedKey = $request->header('X-API-KEY');
        $expectedKey = config('services.external_api.key');

        if (! $providedKey || ! $expectedKey || ! hash_equals((string) $expectedKey, (string) $providedKey)) {
            return response()->json([
                'message' => 'Unauthorized: missing or invalid API key',
            ], 401);
        }

        return $next($request);
    }
}
