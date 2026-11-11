<?php

namespace App\Modules\Academic\Application\Port;

/**
 * Lo que Academic necesita saber de las personas (datos de IdentityAccess y del expediente).
 */
interface AcademicPeople
{
    /** ¿Es un usuario activo con rol Docente? */
    public function isActiveTeacher(int $userId): bool;

    /**
     * Programas del estudiante (expediente → usuario → program_user); null si el expediente no existe.
     *
     * @return list<int>|null
     */
    public function programsOfStudent(int $studentId): ?array;

    public function displayName(int $userId): string;
}
