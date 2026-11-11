<?php

namespace App\Modules\Academic\Application\Port;

use App\Modules\Academic\Domain\Section;

interface SectionRepository
{
    public function find(int $id): ?Section;

    public function save(Section $section): Section;

    public function delete(int $id): void;

    public function codeTaken(int $lapseId, int $courseId, string $code, ?int $exceptId = null): bool;

    public function hasEnrollments(int $id): bool;

    public function openCountInLapse(int $lapseId): int;
}
