<?php

namespace App\Modules\IdentityAccess\Domain;

/**
 * Matriz RBAC (FUNCTIONAL_SPECIFICATIONS.md §6 y §7): qué permisos tiene cada
 * rol del sistema. Es la única fuente de verdad; el seeder la vuelca en Spatie.
 */
final class AccessPolicy
{
    /** @return list<Permission> */
    public static function permissionsFor(RoleName $role): array
    {
        return match ($role) {
            // Control de Estudios: acceso total.
            RoleName::Administrador => Permission::cases(),

            // Supervisión: lectura amplia, sin operar el día a día ni calificar.
            RoleName::Director => [
                Permission::DashboardView,
                Permission::UsersView,
                Permission::ReportsView,
                Permission::StudentsView,
                Permission::GradesView,
                Permission::DocumentsDownload,
            ],

            // Académico-administrativo limitado a su programa (ver ProgramScope).
            RoleName::Coordinador => [
                Permission::DashboardView,
                Permission::StudentsView,
                Permission::StudentsEnroll,
                Permission::GradesView,
                Permission::DocumentsDownload,
            ],

            // Operativo: inscripción y validación de datos; no toca notas ni lapsos.
            RoleName::Asistente => [
                Permission::DashboardView,
                Permission::StudentsView,
                Permission::StudentsManage,
                Permission::StudentsEnroll,
            ],

            // Califica entregas. La restricción a "sus" alumnos llega con la
            // asignación de jurados (módulo de entregas).
            RoleName::Docente => [
                Permission::GradesRecord,
                Permission::DocumentsDownload,
            ],

            // Acceden solo a su propio expediente: lo controla StudentRecordAccess,
            // no un permiso global.
            RoleName::Estudiante, RoleName::Aspirante => [],
        };
    }
}
