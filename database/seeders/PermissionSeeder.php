<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $resources = [
            'roles' => [
                'name' => 'Permissões',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],

            'users' => [
                'name' => 'Usuários',
                'permissions' => ['index', 'create', 'edit', 'destroy'],
            ],
        ];

        $actions = [
            'index' => 'Visualizar',
            'create' => 'Criar',
            'edit' => 'Editar',
            'destroy' => 'Excluir',
        ];

        foreach ($resources as $resource => $config) {
            foreach ($config['permissions'] as $permission) {
                Permission::firstOrCreate([
                    'name' => "$resource.$permission",
                    'description' => "{$actions[$permission]} {$config['name']}",
                ]);
            }
        }
    }
}
