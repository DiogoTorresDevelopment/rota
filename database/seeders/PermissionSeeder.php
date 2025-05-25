<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = [
            'users' => 'Usuários',
            'drivers' => 'Motoristas',
            'trucks' => 'Caminhões',
            'routes' => 'Rotas',
            'deliveries' => 'Entregas',
            'permissions' => 'Permissões'
        ];

        foreach ($modules as $module => $name) {
            // Permissão de visualização
            DB::table('permissions')->insert([
                'name' => "Ver {$name}",
                'slug' => "{$module}.view",
                'description' => "Visualizar lista de {$name}",
                'type' => 'management',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Permissão de gerenciamento
            DB::table('permissions')->insert([
                'name' => "Gerenciar {$name}",
                'slug' => "{$module}.manage",
                'description' => "Adicionar, editar e remover {$name}",
                'type' => 'management',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
