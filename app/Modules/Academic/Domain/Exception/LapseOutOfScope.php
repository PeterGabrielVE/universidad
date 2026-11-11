<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\AccessDeniedException;

final class LapseOutOfScope extends AccessDeniedException
{
    public function __construct()
    {
        parent::__construct('El lapso o programa no pertenece a tu programa.');
    }
}
