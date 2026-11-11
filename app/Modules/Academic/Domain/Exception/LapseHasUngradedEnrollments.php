<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class LapseHasUngradedEnrollments extends DomainException
{
    public function __construct(string $name, int $count)
    {
        parent::__construct("No se puede cerrar «{$name}»: {$count} inscripción(es) activa(s) sin nota.");
    }

    public function field(): ?string
    {
        return 'status';
    }
}
