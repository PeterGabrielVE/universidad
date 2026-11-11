<?php

namespace App\Modules\Academic\Domain;

/**
 * Estado de una inscripción en un lapso (FUNCTIONAL_SPECIFICATIONS.md §10).
 */
enum EnrollmentStatus: string
{
    /** Falta pago o validación. */
    case PreInscrito = 'pre_inscrito';
    /** Cursando. Es la única que se evalúa al cerrar el lapso. */
    case Activo = 'activo';
    /** Retiro temporal. */
    case Congelado = 'congelado';
    /** Terminó el lapso; su resultado queda fijo. */
    case Cerrado = 'cerrado';

    public function label(): string
    {
        return match ($this) {
            self::PreInscrito => 'Preinscrito',
            self::Activo => 'Activo',
            self::Congelado => 'Congelado',
            self::Cerrado => 'Cerrado',
        };
    }
}
