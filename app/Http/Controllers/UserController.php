<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.view')->only(['index', 'show']);
        $this->middleware('permission:users.manage')->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $users = User::with('permissionGroups')
            ->where('id', '!=', auth()->id())
            ->get();
        return view('users.index', compact('users'));
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function create()
    {
        $permissionGroups = PermissionGroup::where('status', true)->get();
        return view('users.create', compact('permissionGroups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'permission_group' => 'required|exists:permission_groups,id',
            'status' => 'boolean'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status ?? true
        ]);

        $user->permissionGroups()->attach($request->permission_group);

        return redirect()->route('users.index')
            ->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        $permissionGroups = PermissionGroup::where('status', true)->get();
        return view('users.edit', compact('user', 'permissionGroups'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'permission_group' => 'required|exists:permission_groups,id',
            'status' => 'boolean'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status ?? true
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->permissionGroups()->sync([$request->permission_group]);

        return redirect()->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json([
                'success' => true,
                'message' => 'Usuário excluído com sucesso.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir usuário: ' . $e->getMessage()
            ], 500);
        }
    }
} 