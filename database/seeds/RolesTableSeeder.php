<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class RolesTableSeeder extends Seeder
{
    /**
     * Los ids deben coincidir con users.rol_id (ver UserController::store).
     */
    public function run(): void
    {
        $roles = [
            1 => ['Director', 'Dirección general de la institución'],
            2 => ['Coordinador', 'Coordinación académica de programas'],
            3 => ['Asistente', 'Asistente de postgrado'],
            4 => ['Estudiante', 'Estudiante inscrito'],
            5 => ['Administrador', 'Administración total del sistema'],
        ];

        foreach ($roles as $id => [$name, $description]) {
            DB::table('roles')->updateOrInsert(
                ['id' => $id],
                [
                    'name' => $name,
                    'guard_name' => 'web',
                    'description' => $description,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
