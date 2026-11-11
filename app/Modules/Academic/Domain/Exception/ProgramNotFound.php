<?php

namespace App\Modules\Academic\Domain\Exception;

use App\Modules\Shared\Domain\NotFoundException;

final class ProgramNotFound extends NotFoundException
{
    public function __construct(int $id)
    {
        parent::__construct("No existe el programa {$id}.");
    }

    public function field(): ?string
    {
        return 'program_id';
    }
}
