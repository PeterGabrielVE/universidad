<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\DomainException;

final class LapseNotOpen extends DomainException
{
    public function __construct(string $action)
    {
        parent::__construct("El lapso no está abierto: no se puede {$action}.");
    }
}
