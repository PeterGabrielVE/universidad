<?php

namespace App\Modules\Academic\Domain;

/**
 * Resultado de una inscripción, fijado al cerrar el lapso para que el récord no
 * cambie si después el programa modifica su nota mínima (§8).
 */
enum EnrollmentResult: string
{
    case Aprobado = 'aprobado';
    case Reprobado = 'reprobado';

    public function label(): string
    {
        return ucfirst($this->value);
    }
}
