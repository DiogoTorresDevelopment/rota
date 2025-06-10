<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $guard = null)
    {
        // Verifica se o usuário está autenticado em qualquer guard
        if (Auth::guard('web')->check() || Auth::guard('api')->check() || Auth::guard('driver')->check()) {
            // Se a requisição for para a página de login ou logout, permite continuar
            if ($request->routeIs('login') || $request->routeIs('logout')) {
                return $next($request);
            }

            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
