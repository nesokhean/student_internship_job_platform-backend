<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if ($user->status === 'blocked' || $user->status === 'suspended') {
            $user->tokens()->delete();

            return response()->json([
                'success' => false,
                'message' => 'Your account is currently unavailable.',
            ], 403);
        }

        return $next($request);
    }
}
