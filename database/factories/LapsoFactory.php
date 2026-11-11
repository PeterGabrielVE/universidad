<?php

namespace Database\Factories;

use App\Lapso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Lapso académico. status: '1' abierto, '0' cerrado (formato actual; ver
 * FUNCTIONAL_SPECIFICATIONS.md §10 para el vocabulario objetivo).
 *
 * @extends Factory<Lapso>
 */
class LapsoFactory extends Factory
{
    protected $model = Lapso::class;

    public function definition(): array
    {
        return [
            'name' => 'Lapso Enero 2026-Junio 2026',
            'start_lapse' => '2026-01-12',
            'end_lapse' => '2026-06-26',
            'status' => '1',
        ];
    }

    public function closed(): static
    {
        return $this->state(['status' => '0']);
    }
}
