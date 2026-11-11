<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\AccessDeniedException;

final class StudentRecordOutOfScope extends AccessDeniedException
{
    public function __construct()
    {
        parent::__construct('Este expediente no es tuyo ni está en tu alcance.');
    }
}
