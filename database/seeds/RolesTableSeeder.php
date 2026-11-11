<?php

use App\Modules\IdentityAccess\Domain\AccessPolicy;
use App\Modules\IdentityAccess\Domain\Permission as PermissionName;
use App\Modules\IdentityAccess\Domain\RoleName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Roles del sistema y sus permisos, volcados desde AccessPolicy (dominio).
 * Idempotente: se puede ejecutar tras cada cambio de la matriz RBAC.
 */
class RolesTableSeeder extends Seeder
{
    /** Ids históricos (1–5) conservados; 6 y 7 son los roles nuevos de la Fase 2. */
    private const ROLE_IDS = [
        1 => RoleName::Director,
        2 => RoleName::Coordinador,
        3 => RoleName::Asistente,
        4 => RoleName::Estudiante,
        5 => RoleName::Administrador,
        6 => RoleName::Docente,
        7 => RoleName::Aspirante,
    ];

    public function run(): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->forgetCachedPermissions();

        foreach (PermissionName::cases() as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        foreach (self::ROLE_IDS as $id => $role) {
            DB::table('roles')->updateOrInsert(
                ['id' => $id],
                [
                    'name' => $role->value,
                    'guard_name' => 'web',
                    'description' => $role->description(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            Role::findById($id, 'web')->syncPermissions(
                array_map(fn (PermissionName $p) => $p->value, AccessPolicy::permissionsFor($role))
            );
        }

        $registrar->forgetCachedPermissions();
    }
}
