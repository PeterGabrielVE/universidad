<?php

namespace App\Modules\Academic\Application\UseCase;

use App\Modules\Academic\Application\Port\LapseRepository;
use App\Modules\Academic\Application\Port\ProgramRepository;
use App\Modules\Academic\Domain\Exception\LapseNotFound;
use App\Modules\Academic\Domain\Exception\LapseOutOfScope;
use App\Modules\Academic\Domain\Exception\ProgramNotFound;
use App\Modules\Academic\Domain\Lapse;
use App\Modules\Academic\Domain\LapsePeriod;
use App\Modules\Academic\Domain\LapseStatus;
use App\Modules\IdentityAccess\Domain\ProgramScope;

/**
 * Alta, edición y baja de lapsos. $scope es el alcance del usuario: el
 * Administrador opera sobre todos, el Coordinador solo sobre su programa (§7).
 */
final class ManageLapses
{
    public function __construct(
        private readonly LapseRepository $lapses,
        private readonly ProgramRepository $programs,
        private readonly CloseLapse $closeLapse,
    ) {
    }

    public function create(int $programId, string $start, string $end, LapseStatus $initialStatus, ProgramScope $scope): Lapse
    {
        if ($this->programs->find($programId) === null) {
            throw new ProgramNotFound($programId);
        }

        if (! $scope->covers($programId)) {
            throw new LapseOutOfScope();
        }

        return $this->lapses->save(Lapse::plan($programId, LapsePeriod::between($start, $end), $initialStatus));
    }

    /** Cambiar el estado a "cerrado" ejecuta el cierre completo (CloseLapse). */
    public function update(int $id, string $start, string $end, LapseStatus $status, ProgramScope $scope): Lapse
    {
        $lapse = $this->load($id, $scope);
        $lapse->reschedule(LapsePeriod::between($start, $end));

        if ($status === LapseStatus::Cerrado) {
            return $this->closeLapse->close($lapse);
        }

        $lapse->changeStatus($status);

        return $this->lapses->save($lapse);
    }

    public function delete(int $id, ProgramScope $scope): void
    {
        $lapse = $this->load($id, $scope);
        $lapse->assertDeletable();

        $this->lapses->delete($id);
    }

    public function get(int $id, ProgramScope $scope): Lapse
    {
        return $this->load($id, $scope);
    }

    private function load(int $id, ProgramScope $scope): Lapse
    {
        $lapse = $this->lapses->find($id) ?? throw new LapseNotFound($id);

        if (! $scope->covers($lapse->programId())) {
            throw new LapseOutOfScope();
        }

        return $lapse;
    }
}
