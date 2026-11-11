<?php

namespace App\Modules\Academic\Domain;

use App\Modules\Academic\Domain\Exception\InvalidLapseTransition;
use App\Modules\Academic\Domain\Exception\LapseCannotBeDeleted;
use App\Modules\Academic\Domain\Exception\LapseIsClosed;

/**
 * Lapso o cohorte de un programa. Un lapso cerrado es inmutable: sus notas son
 * definitivas y forman el récord académico (FUNCTIONAL_SPECIFICATIONS.md §3).
 *
 * $programId es null solo en lapsos anteriores a la Fase 3, que no tenían programa.
 */
final class Lapse
{
    private function __construct(
        private ?int $id,
        private ?int $programId,
        private LapsePeriod $period,
        private LapseStatus $status,
    ) {
    }

    public static function plan(int $programId, LapsePeriod $period, LapseStatus $initialStatus): self
    {
        if ($initialStatus === LapseStatus::Cerrado) {
            throw InvalidLapseTransition::cannotCreateClosed();
        }

        return new self(null, $programId, $period, $initialStatus);
    }

    /** Solo para adaptadores de persistencia. */
    public static function restore(int $id, ?int $programId, LapsePeriod $period, LapseStatus $status): self
    {
        return new self($id, $programId, $period, $status);
    }

    public function reschedule(LapsePeriod $period): void
    {
        $this->assertNotClosed();
        $this->period = $period;
    }

    /** Planificación → abierto. El cierre va por close(), que exige evaluar inscripciones. */
    public function changeStatus(LapseStatus $target): void
    {
        $this->assertNotClosed();

        if ($target === $this->status) {
            return;
        }

        if ($target === LapseStatus::Cerrado || ! $this->status->canTransitionTo($target)) {
            throw InvalidLapseTransition::between($this->status, $target);
        }

        $this->status = $target;
    }

    public function close(): void
    {
        $this->assertNotClosed();

        if (! $this->status->canTransitionTo(LapseStatus::Cerrado)) {
            throw InvalidLapseTransition::between($this->status, LapseStatus::Cerrado);
        }

        $this->status = LapseStatus::Cerrado;
    }

    /** Solo se elimina un lapso que nunca se abrió: después tiene historia académica. */
    public function assertDeletable(): void
    {
        if ($this->status !== LapseStatus::Planificacion) {
            throw new LapseCannotBeDeleted($this->name());
        }
    }

    public function isClosed(): bool
    {
        return $this->status === LapseStatus::Cerrado;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function programId(): ?int
    {
        return $this->programId;
    }

    public function period(): LapsePeriod
    {
        return $this->period;
    }

    public function status(): LapseStatus
    {
        return $this->status;
    }

    public function name(): string
    {
        return $this->period->name();
    }

    private function assertNotClosed(): void
    {
        if ($this->isClosed()) {
            throw new LapseIsClosed($this->name());
        }
    }
}
