<?php

namespace App\Modules\Academic\Application\Dto;

use App\Modules\Academic\Domain\Grade;

final class ActiveEnrollment
{
    public function __construct(
        public readonly int $id,
        public readonly ?Grade $grade,
    ) {
    }
}
