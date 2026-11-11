<?php

namespace Tests\Feature\Security;

use App\Models\Role;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Hallazgos C3 y C6: la API de roles era pública y la de usuarios
 * aceptaba a cualquier usuario autenticado.
 */
class ApiProtectionTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_guest_cannot_list_roles(): void
    {
        $this->getJson('/api/roles')->assertUnauthorized();
    }

    public function test_guest_cannot_create_roles(): void
    {
        $this->postJson('/api/roles', ['name' => 'Intruso'])->assertUnauthorized();

        $this->assertDatabaseMissing('roles', ['name' => 'Intruso']);
    }

    public function test_guest_cannot_delete_roles(): void
    {
        $this->deleteJson('/api/roles/1')->assertUnauthorized();

        $this->assertNotNull(Role::find(1));
    }

    public function test_student_cannot_manage_roles(): void
    {
        $student = $this->createUser('Estudiante');

        $this->actingAs($student, 'sanctum')->getJson('/api/roles')->assertForbidden();
        $this->actingAs($student, 'sanctum')->deleteJson('/api/roles/1')->assertForbidden();
    }

    public function test_student_cannot_manage_users_through_the_api(): void
    {
        $student = $this->createUser('Estudiante');
        $other = $this->createUser('Asistente');

        $this->actingAs($student, 'sanctum')->getJson('/api/v1/users')->assertForbidden();
        $this->actingAs($student, 'sanctum')->deleteJson("/api/v1/users/{$other->id}")->assertForbidden();

        $this->assertNotNull(User::find($other->id));
    }

    public function test_admin_can_list_roles(): void
    {
        $admin = $this->createUser('Director');

        $this->actingAs($admin, 'sanctum')->getJson('/api/roles')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Estudiante']);
    }

    /**
     * Las pantallas Vue llaman a la API con la cookie de sesión (Sanctum en modo SPA),
     * sin tokens Bearer. Se verifica que el dominio local del docker-compose es "stateful".
     */
    public function test_admin_session_cookie_authenticates_api_from_local_frontend(): void
    {
        $admin = $this->createUser('Administrador');

        $this->actingAs($admin)
            ->withHeader('Referer', 'http://localhost:8000/roles')
            ->getJson('/api/roles')
            ->assertOk();
    }

    /**
     * Regresión: sin limitador "api" definido, throttle:api permitía 0 peticiones
     * y la segunda llamada del mismo minuto (p. ej. listar y luego eliminar) daba 429.
     */
    public function test_consecutive_api_calls_are_not_throttled(): void
    {
        $admin = $this->createUser('Director');

        foreach (range(1, 3) as $attempt) {
            $this->actingAs($admin, 'sanctum')->getJson('/api/roles')->assertOk();
        }
    }

    public function test_admin_can_list_users_through_the_api(): void
    {
        $admin = $this->createUser('Administrador');

        $this->actingAs($admin, 'sanctum')->getJson('/api/v1/users')->assertOk();
    }
}
