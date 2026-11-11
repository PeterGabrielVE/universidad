<?php

namespace Tests\Concerns;

use App\Estudiante;
use App\User;
use RolesTableSeeder;

/**
 * Atajos sobre las factories para las pruebas de Feature.
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
        $factory = User::factory();

        if ($role !== null) {
            $factory = $factory->withRole($role);
        }

        return $factory->create($attributes);
    }

    protected function createStudent(array $userAttributes = []): Estudiante
    {
        return Estudiante::factory()
            ->for(User::factory()->withRole('Estudiante')->state($userAttributes), 'user')
            ->create();
    }
}
