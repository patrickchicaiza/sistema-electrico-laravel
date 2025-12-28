<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // Database\Seeders\PermissionTableSeeder.php
    public function run(): void
    {
        $permissions = [
            // Permisos de Usuarios
            'ver-usuarios',
            'crear-usuarios',
            'editar-usuarios',
            'eliminar-usuarios',

            // Permisos de Reportes (los agregaremos después)
            'ver-reportes',
            'crear-reportes',
            'editar-reportes',
            'eliminar-reportes',
            'asignar-reportes', // Para administradores

            // Permisos de Roles (opcional, si usas CRUD de roles)
            'ver-roles',
            'crear-roles',
            'editar-roles',
            'eliminar-roles',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
}