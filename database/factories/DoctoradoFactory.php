<?php

namespace Database\Factories;

use App\Doctorado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Programa académico (tabla "doctorados") con la nota mínima base.
 *
 * @extends Factory<Doctorado>
 */
class DoctoradoFactory extends Factory
{
    protected $model = Doctorado::class;

    public function definition(): array
    {
        return [
            'name' => 'Doctorado en ' . $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(),
            'min_passing_grade' => 48,
        ];
    }
}
