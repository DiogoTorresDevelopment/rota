<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Log;

class DriverApiMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        try {
            Log::info('DriverApiMiddleware: Iniciando autenticação', [
                'token' => $request->bearerToken()
            ]);

            // Garante que o guard 'driver' será usado
            auth()->shouldUse('driver');
            $user = \PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth::setToken($request->bearerToken())->toUser();
            
            Log::info('DriverApiMiddleware: Usuário autenticado', [
                'user' => $user,
                'class' => get_class($user)
            ]);

            if (!$user) {
                Log::warning('DriverApiMiddleware: Usuário não encontrado');
                return response()->json(['error' => 'Unauthorized. User not found.'], 401);
            }

            // Verifica se é um Driver ou se tem relacionamento com Driver
            if (!($user instanceof \App\Models\Driver) && !$user->driver) {
                Log::warning('DriverApiMiddleware: Usuário não é um motorista', [
                    'user_id' => $user->id,
                    'user_type' => get_class($user)
                ]);
                return response()->json(['error' => 'Unauthorized. Not a driver.'], 401);
            }

            if (!$user->status) {
                Log::warning('DriverApiMiddleware: Conta desativada', [
                    'user_id' => $user->id
                ]);
                return response()->json(['error' => 'Account is disabled.'], 403);
            }

            Log::info('DriverApiMiddleware: Autenticação bem sucedida', [
                'user_id' => $user->id
            ]);

            return $next($request);
        } catch (\Exception $e) {
            Log::error('DriverApiMiddleware: Erro na autenticação', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

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