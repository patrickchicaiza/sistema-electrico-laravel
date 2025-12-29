<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // PRIMERO: Crear todos los roles del sistema
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $adminRole = Role::firstOrCreate(['name' => 'administrador']);
        $tecnicoRole = Role::firstOrCreate(['name' => 'tecnico']);
        $clienteRole = Role::firstOrCreate(['name' => 'cliente']);

        // Asignar TODOS los permisos a super_admin
        $superAdminRole->syncPermissions(Permission::all());

        // Asignar permisos específicos a administrador
        $adminRole->syncPermissions([
            'ver-usuarios',
            'crear-usuarios',
            'editar-usuarios',
            'ver-reportes',
            'editar-reportes',
            'asignar-reportes'
        ]);

        // Asignar permisos a técnico
        $tecnicoRole->syncPermissions([
            'ver-reportes',
            'editar-reportes' // Solo sus reportes asignados
        ]);

        // Asignar permisos a cliente
        $clienteRole->syncPermissions([
            'crear-reportes',
            'ver-reportes', // Solo sus reportes
            'editar-reportes'  // 
        ]);
        // SEGUNDO: Crear usuarios de prueba

        // 1. Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Patrick Chicaiza',
                'password' => bcrypt('admin123456'),
                'telefono' => '0991234567'
            ]
        );
        $superAdmin->syncRoles([$superAdminRole->name]);

        // 2. Administrador
        $admin = User::firstOrCreate(
            ['email' => 'carlos@electrica.com'],
            [
                'name' => 'Carlos Admin',
                'password' => bcrypt('admin123'),
                'telefono' => '0991111111'
            ]
        );
        $admin->syncRoles([$adminRole->name]);

        // 3. Técnico
        $tecnico = User::firstOrCreate(
            ['email' => 'tecnico@electrica.com'],
            [
                'name' => 'Juan Técnico',
                'password' => bcrypt('tecnico123'),
                'telefono' => '0992222222',
                'direccion' => 'Av. Amazonas, Quito'
            ]
        );
        $tecnico->syncRoles([$tecnicoRole->name]);

        // 4. Cliente
        $cliente = User::firstOrCreate(
            ['email' => 'cliente@gmail.com'],
            [
                'name' => 'John Doe',
                'password' => bcrypt('cliente123'),
                'telefono' => '0993333333',
                'direccion' => 'Calle 123, Quito'
            ]
        );
        $cliente->syncRoles([$clienteRole->name]);

        // 5. Otro cliente para pruebas
        $cliente2 = User::firstOrCreate(
            ['email' => 'maria@gmail.com'],
            [
                'name' => 'María García',
                'password' => bcrypt('cliente123'),
                'telefono' => '0994444444',
                'direccion' => 'Av. Shyris, Quito'
            ]
        );
        $cliente2->syncRoles([$clienteRole->name]);

        echo "Seeder ejecutado exitosamente!\n";
        echo "Usuarios creados:\n";
        echo "1. Super Admin: admin@gmail.com / admin123456\n";
        echo "2. Administrador: carlos@electrica.com / admin123\n";
        echo "3. Técnico: tecnico@electrica.com / tecnico123\n";
        echo "4. Cliente: cliente@gmail.com / cliente123\n";
        echo "5. Cliente 2: maria@gmail.com / cliente123\n";
    }
}