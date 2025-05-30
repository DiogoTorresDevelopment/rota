<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Driver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Carrega o usuário com suas permissões
            $user = Auth::user()->load('permissionGroups.permissions');
            
            // Armazena as permissões na sessão
            $permissions = [];
            foreach ($user->permissionGroups as $group) {
                foreach ($group->permissions as $permission) {
                    $permissions[] = $permission->slug;
                }
            }
            session(['user_permissions' => $permissions]);

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function checkAuth()
    {
        if (!Auth::check()) {
            return response()->json(['authenticated' => false], 401);
        }
        return response()->json(['authenticated' => true, 'user' => Auth::user()]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => true
        ]);

        Auth::login($user);

        return redirect('/');
    }

    public function apiLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'type' => 'required|in:user,driver'
        ]);

        if ($request->type === 'driver') {
            $driver = \App\Models\Driver::where('email', $request->email)->first();

            if (!$driver || !Hash::check($request->password, $driver->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciais inválidas'
                ], 401);
            }

            if (!$driver->status) {
                return response()->json([
                    'success' => false,
                    'message' => 'Conta desativada'
                ], 403);
            }

            $token = $driver->createToken('driver-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $driver->id,
                        'name' => $driver->name,
                        'email' => $driver->email,
                        'type' => 'driver',
                        'driver' => [
                            'id' => $driver->id,
                            'name' => $driver->name,
                            'phone' => $driver->phone,
                            'status' => $driver->status,
                        ]
                    ]
                ]
            ]);
        } else {
            $user = User::where('email', $request->email)->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciais inválidas'
                ], 401);
            }

            // Verifica se o usuário é um motorista
            if (!$user->hasRole('driver')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Acesso permitido apenas para motoristas'
                ], 403);
            }

            $token = $user->createToken('driver-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'type' => 'user',
                        'driver' => $user->driver ? [
                            'id' => $user->driver->id,
                            'name' => $user->driver->name,
                            'phone' => $user->driver->phone,
                            'status' => $user->driver->status,
                        ] : null
                    ]
                ]
            ]);
        }
    }

    public function apiLogout(Request $request)
    {
        try {
            if (!$request->user()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não autenticado'
                ], 401);
            }

            // Revoga o token atual do usuário
            $request->user()->currentAccessToken()->delete();
            
            \Log::info('Logout realizado com sucesso', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Logout realizado com sucesso'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao realizar logout', [
                'error' => $e->getMessage(),
                'user_id' => $request->user()->id ?? null
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar logout'
            ], 500);
        }
    }

    public function apiDriverLogin(Request $request)
    {
        try {
            \Log::info('Tentativa de login do motorista', ['email' => $request->email]);

            $request->validate([
                'email' => 'required|email',
                'password' => 'required'
            ]);

            $driver = Driver::where('email', $request->email)->first();

            if (!$driver) {
                \Log::warning('Motorista não encontrado', ['email' => $request->email]);
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciais inválidas'
                ], 401);
            }

            if (!Hash::check($request->password, $driver->password)) {
                \Log::warning('Senha incorreta para o motorista', ['email' => $request->email]);
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciais inválidas'
                ], 401);
            }

            if (!$driver->status) {
                \Log::warning('Tentativa de login de motorista inativo', ['email' => $request->email]);
                return response()->json([
                    'success' => false,
                    'message' => 'Conta desativada'
                ], 403);
            }

            // Revoga todos os tokens antigos
            $driver->tokens()->delete();
            
            // Gera um novo token usando Sanctum
            $token = $driver->createToken('driver-token', ['*'])->plainTextToken;

            \Log::info('Login do motorista realizado com sucesso', [
                'email' => $request->email,
                'token' => $token
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $driver->id,
                        'name' => $driver->name,
                        'email' => $driver->email,
                        'type' => 'driver',
                        'driver' => [
                            'id' => $driver->id,
                            'name' => $driver->name,
                            'phone' => $driver->phone,
                            'status' => $driver->status,
                        ]
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro no login do motorista', [
                'email' => $request->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao realizar login',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
