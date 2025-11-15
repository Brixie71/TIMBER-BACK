<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SkipCsrfForApi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if this is an API request
        if (strpos($request->path(), 'api/') === 0) {
            // This is an API request, so we'll skip CSRF verification
            return $next($request);
        }

        // For non-API requests, continue with normal CSRF checks
        return app(\App\Http\Middleware\VerifyCsrfToken::class)->handle($request, $next);
    }
}