<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class LapseHasOpenGradeSheets extends DomainException
{
    public function __construct(string $lapseName, int $count)
    {
        parent::__construct(
            "No se puede cerrar «{$lapseName}»: {$count} acta(s) siguen abiertas. "
            . 'Los docentes deben cerrarlas o Control de Estudios aplicar el cierre administrativo forzoso.'
        );
    }

    public function field(): ?string
    {
        return 'status';
    }
}
