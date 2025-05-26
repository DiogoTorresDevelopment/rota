<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        logger()->info('REQUEST', [
            'ip' => $request->ip(),
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'input' => $request->all(),
            'headers' => $request->headers->all(),
            'user_agent' => $request->userAgent(),
        ]);

        $response = $next($request);

        logger()->info('RESPONSE', [
            'status' => $response->status(),
            'headers' => $response->headers->all(),
        ]);

        return $response;
    }
}
