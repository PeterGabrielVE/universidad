<?php

namespace Database\Factories;

use App\Doctorado;
use App\Lapso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Lapso abierto de un programa (crea el programa si no se indica).
 *
 * @extends Factory<Lapso>
 */
class LapsoFactory extends Factory
{
    protected $model = Lapso::class;

    public function definition(): array
    {
        return [
            'program_id' => Doctorado::factory(),
            'name' => 'Lapso Enero 2026-Junio 2026',
            'start_lapse' => '2026-01-12',
            'end_lapse' => '2026-06-26',
            'status' => 'abierto',
        ];
    }

    public function planned(): static
    {
        return $this->state(['status' => 'planificacion']);
    }

    public function closed(): static
    {
        return $this->state(['status' => 'cerrado']);
    }
}
