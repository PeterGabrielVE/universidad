<?php

namespace App\Modules\Academic\Application\UseCase;

use App\Modules\Academic\Application\Port\PensumRepository;
use App\Modules\Academic\Application\Port\ProgramRepository;
use App\Modules\Academic\Domain\Exception\CourseNotFound;
use App\Modules\Academic\Domain\Exception\ProgramNotFound;

/**
 * Asignaturas de un programa (opción A confirmada: N:M, §15).
 */
final class ManagePensum
{
    public function __construct(
        private readonly ProgramRepository $programs,
        private readonly PensumRepository $pensum,
    ) {
    }

    public function attach(int $programId, int $courseId): void
    {
        $this->assertExists($programId, $courseId);
        $this->pensum->attach($programId, $courseId);
    }

    public function detach(int $programId, int $courseId): void
    {
        $this->assertExists($programId, $courseId);
        $this->pensum->detach($programId, $courseId);
    }

    private function assertExists(int $programId, int $courseId): void
    {
        if ($this->programs->find($programId) === null) {
            throw new ProgramNotFound($programId);
        }

        if (! $this->pensum->courseExists($courseId)) {
            throw new CourseNotFound($courseId);
        }
    }
}
