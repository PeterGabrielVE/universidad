<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\AccessDeniedException;

final class NotSectionTeacher extends AccessDeniedException
{
    public function __construct()
    {
        parent::__construct('Solo el docente titular de la sección puede operar su acta.');
    }
}
