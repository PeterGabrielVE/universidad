<?php

namespace App\Modules\Academic\Application\Dto;

use App\Modules\Academic\Domain\PassingGrade;

final class ProgramData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?int $facultyId,
        public readonly PassingGrade $passingGrade,
    ) {
    }
}
