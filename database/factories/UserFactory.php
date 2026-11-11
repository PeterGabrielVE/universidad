<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Usuario del modelo de autenticación (App\User). Con ->withRole() deja
 * coherentes users.rol_id y el rol de Spatie, como hace UserController::store.
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /** Ids de la tabla roles (RolesTableSeeder). */
    private const ROLE_IDS = [
        'Director' => 1,
        'Coordinador' => 2,
        'Asistente' => 3,
        'Estudiante' => 4,
        'Administrador' => 5,
    ];

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'identification_card' => $this->faker->unique()->numberBetween(1_000_000, 99_999_999),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
        ];
    }

    public function withRole(string $role): static
    {
        return $this
            ->state(['rol_id' => self::ROLE_IDS[$role]])
            ->afterCreating(fn (User $user) => $user->assignRole($role));
    }
}
