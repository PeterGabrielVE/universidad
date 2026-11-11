<?php

namespace App\Modules\Academic\Application\UseCase;

use App\Modules\Academic\Application\Dto\ActiveEnrollment;
use App\Modules\Academic\Application\Port\EnrollmentRepository;
use App\Modules\Academic\Application\Port\LapseRepository;
use App\Modules\Academic\Application\Port\ProgramRepository;
use App\Modules\Academic\Application\Port\SectionRepository;
use App\Modules\Academic\Domain\Exception\LapseHasOpenGradeSheets;
use App\Modules\Academic\Domain\Exception\LapseHasUngradedEnrollments;
use App\Modules\Academic\Domain\Lapse;
use App\Modules\Academic\Domain\PassingGrade;
use App\Modules\Shared\Application\Port\TransactionManager;

/**
 * Cierre de un lapso (FUNCTIONAL_SPECIFICATIONS.md §3 y §8):
 * - Solo evalúa las inscripciones ACTIVAS de ESE lapso; las preinscritas, congeladas
 *   y las de otros lapsos no se tocan.
 * - Compara cada nota con la mínima del programa y guarda el resultado.
 * - No cierra si queda alguna acta abierta (§17) ni alguna inscripción activa sin nota.
 * - Guarda la nota mínima aplicada para que las correcciones posteriores la reutilicen.
 * - Todo o nada: una sola transacción.
 */
final class CloseLapse
{
    public function __construct(
        private readonly LapseRepository $lapses,
        private readonly ProgramRepository $programs,
        private readonly EnrollmentRepository $enrollments,
        private readonly SectionRepository $sections,
        private readonly TransactionManager $transaction,
    ) {
    }

    /** Recibe el lapso ya cargado y autorizado por el caso de uso que lo invoca. */
    public function close(Lapse $lapse): Lapse
    {
        $passingGrade = $this->passingGradeFor($lapse);
        $lapse->close($passingGrade);

        $openSheets = $this->sections->openCountInLapse($lapse->id());
        if ($openSheets > 0) {
            throw new LapseHasOpenGradeSheets($lapse->name(), $openSheets);
        }

        $active = $this->enrollments->activeInLapse($lapse->id());
        $ungraded = array_filter($active, fn (ActiveEnrollment $e) => $e->grade === null);

        if ($ungraded !== []) {
            throw new LapseHasUngradedEnrollments($lapse->name(), count($ungraded));
        }

        return $this->transaction->run(function () use ($lapse, $active, $passingGrade) {
            foreach ($active as $enrollment) {
                $this->enrollments->close($enrollment->id, $passingGrade->evaluate($enrollment->grade));
            }

            return $this->lapses->save($lapse);
        });
    }

    /** Los lapsos anteriores a la Fase 3 no tienen programa: usan la nota base. */
    private function passingGradeFor(Lapse $lapse): PassingGrade
    {
        if ($lapse->programId() === null) {
            return PassingGrade::default();
        }

        return $this->programs->find($lapse->programId())?->passingGrade ?? PassingGrade::default();
    }
}
