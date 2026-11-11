<?php

namespace Tests\Support\Academic;

use App\Modules\Academic\Application\Port\LapseRepository;
use App\Modules\Academic\Domain\Lapse;

final class InMemoryLapseRepository implements LapseRepository
{
    /** @var array<int, Lapse> */
    public array $lapses = [];

    private int $nextId = 1;

    public function find(int $id): ?Lapse
    {
        return isset($this->lapses[$id]) ? clone $this->lapses[$id] : null;
    }

    public function save(Lapse $lapse): Lapse
    {
        $id = $lapse->id() ?? $this->nextId++;
        $this->lapses[$id] = Lapse::restore($id, $lapse->programId(), $lapse->period(), $lapse->status());

        return clone $this->lapses[$id];
    }

    public function delete(int $id): void
    {
        unset($this->lapses[$id]);
    }
}
