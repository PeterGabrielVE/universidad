<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\NotFoundException;

final class LapseNotFound extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("No existe el lapso {$id}.");
    }
}
