<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Throttle2FA
{
    protected $limiter;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $key = '2fa_' . $request->user()->id;

        if ($this->limiter->tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many verification attempts. Please try again later.',
                'retry_after' => $this->limiter->availableIn($key)
            ], 429);
        }

        $this->limiter->hit($key, 300); // 5 minutes decay

        return $next($request);
    }
} 