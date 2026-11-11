<?php

namespace Tests\Support\Academic;

use App\Modules\Academic\Application\Port\PensumRepository;

final class InMemoryPensumRepository implements PensumRepository
{
    /** @var array<int, true> */
    public array $courses = [];

    /** @var array<string, true> "programa:asignatura" */
    public array $links = [];

    public function withCourses(int ...$ids): self
    {
        foreach ($ids as $id) {
            $this->courses[$id] = true;
        }

        return $this;
    }

    public function courseExists(int $courseId): bool
    {
        return isset($this->courses[$courseId]);
    }

    public function attach(int $programId, int $courseId): void
    {
        $this->links["{$programId}:{$courseId}"] = true;
    }

    public function detach(int $programId, int $courseId): void
    {
        unset($this->links["{$programId}:{$courseId}"]);
    }

    public function has(int $programId, int $courseId): bool
    {
        return isset($this->links["{$programId}:{$courseId}"]);
    }
}
