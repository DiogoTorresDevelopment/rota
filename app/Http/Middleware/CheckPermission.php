<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Verifica se o usuário tem a permissão necessária na sessão
        $userPermissions = session('user_permissions', []);
        
        if (!in_array($permission, $userPermissions)) {
            if ($request->ajax()) {
                return response()->json(['error' => 'Acesso não autorizado.'], 403);
            }
            return redirect()->route('dashboard')->with('error', 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }
} 