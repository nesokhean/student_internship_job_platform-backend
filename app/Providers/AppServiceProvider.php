<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = strtolower(trim($request->input('email', '')));
            return Limit::perMinute(5)->by($email . '|' . $request->ip())->response(function (Request $request, array $headers) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many login attempts. Please try again after 1 minute.',
                ], 429, $headers);
            });
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perHour(3)->by($request->ip())->response(function (Request $request, array $headers) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many accounts created from this IP address. Please try again later.',
                ], 429, $headers);
            });
        });

        RateLimiter::for('auth.sensitive', function (Request $request) {
            $key = $request->ip();
            if ($request->has('email')) {
                $key = strtolower(trim($request->input('email'))) . '|' . $request->ip();
            }
            return Limit::perMinute(3)->by($key)->response(function (Request $request, array $headers) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please try again later.',
                ], 429, $headers);
            });
        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
