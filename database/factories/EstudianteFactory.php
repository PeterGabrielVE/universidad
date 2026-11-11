<?php

namespace Database\Factories;

use App\Estudiante;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Expediente de estudiante con su usuario (rol Estudiante).
 *
 * @extends Factory<Estudiante>
 */
class EstudianteFactory extends Factory
{
    protected $model = Estudiante::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->withRole('Estudiante'),
            'status' => '1',
        ];
    }
}
