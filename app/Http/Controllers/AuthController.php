<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;

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
}
