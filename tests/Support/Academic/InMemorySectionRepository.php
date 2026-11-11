<?php

namespace Tests\Support\Academic;

use App\Modules\Academic\Application\Port\SectionRepository;
use App\Modules\Academic\Domain\Section;

final class InMemorySectionRepository implements SectionRepository
{
    /** @var array<int, Section> */
    public array $sections = [];

    private int $nextId = 1;

    public function __construct(private readonly ?InMemoryEnrollmentRepository $enrollments = null)
    {
    }

    public function find(int $id): ?Section
    {
        return isset($this->sections[$id]) ? clone $this->sections[$id] : null;
    }

    public function save(Section $section): Section
    {
        $id = $section->id() ?? $this->nextId++;
        $this->sections[$id] = Section::restore(
            $id,
            $section->lapseId(),
            $section->courseId(),
            $section->code(),
            $section->teacherId(),
            $section->status(),
            $section->closedBy(),
            $section->closedForcibly(),
        );

        return clone $this->sections[$id];
    }

    public function delete(int $id): void
    {
        unset($this->sections[$id]);
    }

    public function codeTaken(int $lapseId, int $courseId, string $code, ?int $exceptId = null): bool
    {
        foreach ($this->sections as $id => $s) {
            if ($id !== $exceptId && $s->lapseId() === $lapseId && $s->courseId() === $courseId && $s->code() === $code) {
                return true;
            }
        }

        return false;
    }

    public function hasEnrollments(int $id): bool
    {
        return $this->enrollments !== null && $this->enrollments->inSection($id) !== [];
    }

    public function openCountInLapse(int $lapseId): int
    {
        return count(array_filter($this->sections, fn (Section $s) => $s->lapseId() === $lapseId && $s->isOpen()));
    }
}
