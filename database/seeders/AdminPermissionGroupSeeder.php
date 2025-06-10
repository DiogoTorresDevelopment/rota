<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminPermissionGroupSeeder extends Seeder
{
    public function run()
    {
        // Criar grupo de permissões de administrador
        $adminGroupId = DB::table('permission_groups')->insertGetId([
            'name' => 'Administrador',
            'status' => 'ativo',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Pegar todas as permissões
        $permissions = DB::table('permissions')->get();

        // Associar todas as permissões ao grupo de administrador
        foreach ($permissions as $permission) {
            DB::table('permission_group_has_permissions')->insert([
                'permission_group_id' => $adminGroupId,
                'permission_id' => $permission->id
            ]);
        }

        // Pegar o ID do usuário root
        $rootUserId = DB::table('users')
            ->where('email', 'root@admin.com')
            ->value('id');

        // Associar o usuário root ao grupo de administrador
        DB::table('user_has_permission_groups')->insert([
            'user_id' => $rootUserId,
            'permission_group_id' => $adminGroupId,
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
} 