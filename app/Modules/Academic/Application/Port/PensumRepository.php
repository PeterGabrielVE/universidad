<?php

namespace App\Modules\Academic\Application\Port;

/**
 * Pensum: asignaturas de cada programa (N:M). Una asignatura transversal
 * (p. ej. Metodología I) existe una vez y pertenece a varios programas.
 */
interface PensumRepository
{
    public function courseExists(int $courseId): bool;

    public function contains(int $programId, int $courseId): bool;

    /** Idempotente: añadir una asignatura que ya está no hace nada. */
    public function attach(int $programId, int $courseId): void;

    public function detach(int $programId, int $courseId): void;
}
