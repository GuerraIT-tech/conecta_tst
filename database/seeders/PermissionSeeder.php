<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // User permissions
            'acesso-administrador',
            'usuario-leitura',
            'usuario-criar',
            'usuario-editar',
            'usuario-deletar',
            
            // Add other permissions as needed
            'permissao-leitura',
            'permissao-criar',
            'permissao-editar',
            'permissao-deletar',
            
            // Add more based on your application needs
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission],
                ['guard_name' => 'web']
            );
        }

        // Create admin role with all permissions
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            ['guard_name' => 'web']
        );
        $adminRole->syncPermissions(Permission::all());

        echo "✅ Permissions created successfully!\n";
    }
}