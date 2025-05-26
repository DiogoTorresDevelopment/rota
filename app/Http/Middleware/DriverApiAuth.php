<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Driver;

class DriverApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        if (!$token) {
            return response()->json(['message' => 'Token não fornecido'], 401);
        }

        // Remove o prefixo 'Bearer ' se existir
        $token = str_replace('Bearer ', '', $token);

        $driver = Driver::where('api_token', $token)->first();
        if (!$driver) {
            return response()->json(['message' => 'Token inválido'], 401);
        }

        // Adiciona o motorista à requisição para uso posterior
        $request->merge(['driver' => $driver]);

        return $next($request);
    }
} 