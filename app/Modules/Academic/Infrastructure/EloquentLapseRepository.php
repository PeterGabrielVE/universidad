<?php

namespace App\Modules\Academic\Infrastructure;

use App\Lapso;
use App\Modules\Academic\Application\Port\LapseRepository;
use App\Modules\Academic\Domain\Lapse;
use App\Modules\Academic\Domain\LapsePeriod;

final class EloquentLapseRepository implements LapseRepository
{
    public function find(int $id): ?Lapse
    {
        $model = Lapso::find($id);

        return $model === null ? null : $this->toDomain($model);
    }

    public function save(Lapse $lapse): Lapse
    {
        $model = $lapse->id() === null ? new Lapso() : Lapso::findOrFail($lapse->id());

        $model->forceFill([
            'program_id' => $lapse->programId(),
            'name' => $lapse->name(),
            'start_lapse' => $lapse->period()->start->format('Y-m-d'),
            'end_lapse' => $lapse->period()->end->format('Y-m-d'),
            'status' => $lapse->status(),
        ])->save();

        return $this->toDomain($model);
    }

    public function delete(int $id): void
    {
        Lapso::whereKey($id)->delete();
    }

    private function toDomain(Lapso $model): Lapse
    {
        return Lapse::restore(
            $model->id,
            $model->program_id,
            LapsePeriod::between(
                substr((string) $model->getRawOriginal('start_lapse'), 0, 10),
                substr((string) $model->getRawOriginal('end_lapse'), 0, 10),
            ),
            $model->status,
        );
    }
}
