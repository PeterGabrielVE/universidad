<?php

namespace Tests\Concerns;

use App\Doctorado;
use App\Estudiante;
use App\Models\User;
use RolesTableSeeder;

/**
 * Atajos sobre las factories para las pruebas de Feature.
 *
 * La clave especial 'programs' (lista de ids) asigna programas por program_user;
 * los programas que no existan se crean con ese id.
 */
trait CreatesUsers
{
    protected function seedRoles(): void
    {
        // Los seeders viven en el namespace global (classmap); db:seed asumiría Database\Seeders.
        $this->app->make(RolesTableSeeder::class)->run();
    }

    protected function createUser(?string $role = null, array $attributes = []): User
    {
        [$programs, $attributes] = $this->splitPrograms($attributes);
        $factory = User::factory()->inPrograms($programs);

        if ($role !== null) {
            $factory = $factory->withRole($role);
        }

        return $factory->create($attributes);
    }

    protected function createStudent(array $userAttributes = []): Estudiante
    {
        [$programs, $userAttributes] = $this->splitPrograms($userAttributes);

        return Estudiante::factory()
            ->for(User::factory()->withRole('Estudiante')->inPrograms($programs)->state($userAttributes), 'user')
            ->create();
    }

    /** @return array{0: list<int>, 1: array} */
    private function splitPrograms(array $attributes): array
    {
        $programs = $attributes['programs'] ?? [];
        unset($attributes['programs']);

        foreach ($programs as $id) {
            Doctorado::query()->find($id) ?? Doctorado::factory()->create(['id' => $id]);
        }

        return [$programs, $attributes];
    }
}
