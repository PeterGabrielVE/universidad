<?php

namespace Tests\Support\Academic;

use App\Modules\Academic\Application\Port\AcademicPeople;

final class FakeAcademicPeople implements AcademicPeople
{
    /** @var array<int, true> */
    public array $teachers = [];

    /** @var array<int, list<int>> id de expediente => programas */
    public array $studentPrograms = [];

    public function isActiveTeacher(int $userId): bool
    {
        return isset($this->teachers[$userId]);
    }

    public function programsOfStudent(int $studentId): ?array
    {
        return $this->studentPrograms[$studentId] ?? null;
    }

    public function displayName(int $userId): string
    {
        return "Usuario {$userId}";
    }
}
