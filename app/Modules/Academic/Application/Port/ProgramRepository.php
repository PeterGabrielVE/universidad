<?php

namespace App\Modules\Academic\Application\Port;

use App\Modules\Academic\Application\Dto\ProgramData;

interface ProgramRepository
{
    public function find(int $id): ?ProgramData;
}
