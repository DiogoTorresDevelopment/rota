<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            // Permissões de Gerenciamento
            [
                'name' => 'Ver caminhões',
                'description' => 'Visualizar lista de caminhões',
                'type' => 'management'
            ],
            [
                'name' => 'Gerenciar caminhões',
                'description' => 'Adicionar, editar e remover caminhões',
                'type' => 'management'
            ],
            [
                'name' => 'Ver motoristas',
                'description' => 'Visualizar lista de motoristas',
                'type' => 'management'
            ],
            [
                'name' => 'Gerenciar motoristas',
                'description' => 'Adicionar, editar e remover motoristas',
                'type' => 'management'
            ],

            // Permissões Operacionais
            [
                'name' => 'Ver rotas',
                'description' => 'Visualizar rotas',
                'type' => 'operational'
            ],
            [
                'name' => 'Gerenciar rotas',
                'description' => 'Adicionar, editar e remover rotas',
                'type' => 'operational'
            ],
            [
                'name' => 'Ver entregas',
                'description' => 'Visualizar entregas',
                'type' => 'operational'
            ],
            [
                'name' => 'Gerenciar entregas',
                'description' => 'Adicionar, editar e remover entregas',
                'type' => 'operational'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::create([
                'name' => $permission['name'],
                'slug' => Str::slug($permission['name']),
                'description' => $permission['description'],
                'type' => $permission['type']
            ]);
        }
    }
}
