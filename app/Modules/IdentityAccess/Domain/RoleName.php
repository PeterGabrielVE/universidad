<?php

namespace App\Modules\IdentityAccess\Domain;

/**
 * Roles del sistema (FUNCTIONAL_SPECIFICATIONS.md §7). Sus permisos están
 * definidos en AccessPolicy y no se pueden renombrar ni eliminar.
 */
enum RoleName: string
{
    case Administrador = 'Administrador';
    case Director = 'Director';
    case Coordinador = 'Coordinador';
    case Asistente = 'Asistente';
    case Docente = 'Docente';
    case Estudiante = 'Estudiante';
    case Aspirante = 'Aspirante';

    public function description(): string
    {
        return match ($this) {
            self::Administrador => 'Control de Estudios: administración total del sistema',
            self::Director => 'Supervisión: lectura de reportes, estadísticas y auditoría',
            self::Coordinador => 'Coordinación académica de su programa',
            self::Asistente => 'Asistencia administrativa: pagos, documentos e inscripción',
            self::Docente => 'Docente o jurado: califica las entregas asignadas',
            self::Estudiante => 'Estudiante inscrito',
            self::Aspirante => 'Aspirante en proceso de admisión',
        };
    }

    public static function isSystemRole(string $name): bool
    {
        return self::tryFrom($name) !== null;
    }
}
