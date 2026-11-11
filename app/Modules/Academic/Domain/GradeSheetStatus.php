<?php

namespace App\Modules\Academic\Domain;

/**
 * Estado del acta de una sección (FUNCTIONAL_SPECIFICATIONS.md §15).
 * Abierta: el docente titular registra notas. Cerrada: solo Control de Estudios
 * puede corregir, con justificación y auditoría.
 */
enum GradeSheetStatus: string
{
    case Abierta = 'abierta';
    case Cerrada = 'cerrada';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
