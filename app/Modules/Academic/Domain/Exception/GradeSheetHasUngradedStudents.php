<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class GradeSheetHasUngradedStudents extends DomainException
{
    public function __construct(int $count)
    {
        parent::__construct("No se puede cerrar el acta: {$count} estudiante(s) activo(s) sin nota.");
    }
}
