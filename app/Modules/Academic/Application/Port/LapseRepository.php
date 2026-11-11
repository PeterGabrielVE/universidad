<?php

namespace App\Modules\Academic\Application\Port;

use App\Modules\Academic\Domain\Lapse;

interface LapseRepository
{
    public function find(int $id): ?Lapse;

    /** Inserta o actualiza; devuelve el lapso con su id. */
    public function save(Lapse $lapse): Lapse;

    public function delete(int $id): void;
}
