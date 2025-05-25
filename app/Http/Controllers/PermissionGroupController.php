<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\PermissionGroup;
use Illuminate\Http\Request;

class PermissionGroupController extends Controller
{
    public function index()
    {
        $groups = PermissionGroup::paginate(50);
        return view('permissions.index', compact('groups'));
    }

    public function show($id)
    {
        $group = PermissionGroup::with('permissions')->findOrFail($id);
        return view('permissions.show', compact('group'));
    }

    public function create()
    {
        $managementPermissions = Permission::where('type', 'management')->get();
        $operationalPermissions = Permission::where('type', 'operational')->get();
        
        return view('permissions.create', compact('managementPermissions', 'operationalPermissions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permission_groups',
            'status' => 'required|in:ativo,inativo',
            'permissions' => 'required|array|exists:permissions,id'
        ]);

        $group = PermissionGroup::create([
            'name' => $validated['name'],
            'status' => $validated['status']
        ]);

        $group->permissions()->attach($request->permissions);

        return redirect()->route('permissions.index')
            ->with('success', 'Grupo de permissões criado com sucesso!');
    }

    public function edit($id)
    {
        $group = PermissionGroup::findOrFail($id);
        $managementPermissions = Permission::where('type', 'management')->get();
        $operationalPermissions = Permission::where('type', 'operational')->get();
        
        return view('permissions.edit', compact('group', 'managementPermissions', 'operationalPermissions'));
    }

    public function update(Request $request, $id)
    {
        $group = PermissionGroup::findOrFail($id);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:permission_groups,name,' . $id,
            'status' => 'required|in:ativo,inativo',
            'permissions' => 'required|array|exists:permissions,id'
        ]);

        $group->update([
            'name' => $validated['name'],
            'status' => $validated['status']
        ]);

        $group->permissions()->sync($request->permissions);

        return redirect()->route('permissions.index')
            ->with('success', 'Grupo de permissões atualizado com sucesso!');
    }

    public function destroy($id)
    {
        try {
            \Log::info('Iniciando exclusão do grupo de permissões: ' . $id);
            
            // Busca o grupo incluindo registros soft deleted
            $group = PermissionGroup::withTrashed()->find($id);
            
            if (!$group) {
                \Log::warning('Grupo de permissões não encontrado: ' . $id);
                return response()->json([
                    'success' => false,
                    'message' => 'Grupo de permissões não encontrado.'
                ], 404);
            }
            
            // Remove as relações primeiro
            $group->permissions()->detach();
            
            // Usa o método delete() do SoftDeletes
            $group->delete();
            
            \Log::info('Grupo de permissões excluído com sucesso: ' . $id);
            
            return response()->json([
                'success' => true,
                'message' => 'Grupo de permissões excluído com sucesso!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Erro ao excluir grupo de permissões: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao excluir grupo de permissões: ' . $e->getMessage()
            ], 500);
        }
    }

    // Adicione outros métodos como edit, update e destroy conforme necessário
} 