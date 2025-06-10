<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;

class DriverApiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            if (!$user || !$user instanceof \App\Models\Driver) {
                return response()->json(['error' => 'Unauthorized. Not a driver.'], 401);
            }

            if (!$user->status) {
                return response()->json(['error' => 'Account is disabled.'], 403);
            }

            return $next($request);
        } catch (\Exception $e) {
            if ($e instanceof TokenInvalidException) {
                return response()->json(['error' => 'Token is invalid'], 401);
            }
            if ($e instanceof TokenExpiredException) {
                return response()->json(['error' => 'Token has expired'], 401);
            }
            return response()->json(['error' => 'Authorization token not found'], 401);
        }
    }
} 