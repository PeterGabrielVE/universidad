<?php

namespace App\Modules\Academic\Application\Dto;

final class FacultyData
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $description,
    ) {
    }
}
