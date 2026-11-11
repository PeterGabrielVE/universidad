<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class GradeSheetClosed extends DomainException
{
    public function __construct(string $sectionCode)
    {
        parent::__construct("El acta de la sección «{$sectionCode}» está cerrada: solo Control de Estudios puede corregir notas.");
    }
}
