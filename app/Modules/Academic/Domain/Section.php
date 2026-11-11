<?php

namespace App\Modules\Academic\Domain;

use App\Modules\Academic\Domain\Exception\GradeSheetClosed;
use App\Modules\Academic\Domain\Exception\NotSectionTeacher;

/**
 * Sección de una asignatura en un lapso, con un único docente titular y su acta
 * (Lapso → Asignatura → Sección → Docente, FUNCTIONAL_SPECIFICATIONS.md §17).
 */
final class Section
{
    private function __construct(
        private ?int $id,
        private int $lapseId,
        private int $courseId,
        private string $code,
        private ?int $teacherId,
        private GradeSheetStatus $status,
        private ?int $closedBy,
        private bool $closedForcibly,
    ) {
    }

    public static function open(int $lapseId, int $courseId, string $code, int $teacherId): self
    {
        return new self(null, $lapseId, $courseId, self::normalizeCode($code), $teacherId, GradeSheetStatus::Abierta, null, false);
    }

    /** Solo para adaptadores de persistencia. */
    public static function restore(
        int $id,
        int $lapseId,
        int $courseId,
        string $code,
        ?int $teacherId,
        GradeSheetStatus $status,
        ?int $closedBy,
        bool $closedForcibly,
    ): self {
        return new self($id, $lapseId, $courseId, $code, $teacherId, $status, $closedBy, $closedForcibly);
    }

    public static function normalizeCode(string $code): string
    {
        return mb_strtoupper(trim($code));
    }

    public function assignTeacher(int $teacherId): void
    {
        $this->assertOpen();
        $this->teacherId = $teacherId;
    }

    /** Solo el docente titular opera el acta abierta. */
    public function assertTaughtBy(int $userId): void
    {
        if ($this->teacherId !== $userId) {
            throw new NotSectionTeacher();
        }
    }

    public function closeByTeacher(int $teacherId): void
    {
        $this->assertTaughtBy($teacherId);
        $this->assertOpen();
        $this->status = GradeSheetStatus::Cerrada;
        $this->closedBy = $teacherId;
        $this->closedForcibly = false;
    }

    /** Cierre administrativo forzoso: el acta queda cerrada bajo la autoría del administrador. */
    public function forceClose(int $administratorId): void
    {
        $this->assertOpen();
        $this->status = GradeSheetStatus::Cerrada;
        $this->closedBy = $administratorId;
        $this->closedForcibly = true;
    }

    public function assertOpen(): void
    {
        if ($this->status === GradeSheetStatus::Cerrada) {
            throw new GradeSheetClosed($this->code);
        }
    }

    public function isOpen(): bool
    {
        return $this->status === GradeSheetStatus::Abierta;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function lapseId(): int
    {
        return $this->lapseId;
    }

    public function courseId(): int
    {
        return $this->courseId;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function teacherId(): ?int
    {
        return $this->teacherId;
    }

    public function status(): GradeSheetStatus
    {
        return $this->status;
    }

    public function closedBy(): ?int
    {
        return $this->closedBy;
    }

    public function closedForcibly(): bool
    {
        return $this->closedForcibly;
    }
}
