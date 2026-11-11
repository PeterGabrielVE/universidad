<?php

namespace App\Modules\Academic\Application\Dto;

use App\Modules\Academic\Domain\PassingGrade;

final class ProgramInput
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $description,
        public readonly int $facultyId,
        public readonly PassingGrade $passingGrade,
    ) {
    }
}
