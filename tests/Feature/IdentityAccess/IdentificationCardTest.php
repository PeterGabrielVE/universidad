<?php

namespace Tests\Feature\IdentityAccess;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * La cédula es única (FUNCTIONAL_SPECIFICATIONS.md §13).
 */
class IdentificationCardTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_registration_with_an_existing_card_is_rejected(): void
    {
        $this->createUser('Estudiante', ['identification_card' => 20111222]);

        $this->from('/register')->post('/register', [
            'name' => 'Otra',
            'last_name' => 'Cuenta',
            'identification_card' => 20111222,
            'email' => 'nueva@gabook.test',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])->assertRedirect('/register')->assertSessionHasErrors('identification_card');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'nueva@gabook.test']);
    }

    public function test_admin_cannot_assign_a_card_that_belongs_to_someone_else(): void
    {
        $admin = $this->createUser('Administrador');
        $owner = $this->createUser('Estudiante', ['identification_card' => 20111222]);
        $other = $this->createUser('Asistente');

        $this->actingAs($admin, 'sanctum')->putJson("/api/v1/users/{$other->id}", [
            'name' => $other->name,
            'last_name' => $other->last_name,
            'identification_card' => $owner->identification_card,
            'email' => $other->email,
            'role' => 'Asistente',
        ])->assertStatus(422)->assertJsonValidationErrors('identification_card');
    }

    public function test_control_de_estudios_can_update_the_email_keeping_the_card(): void
    {
        $admin = $this->createUser('Administrador');
        $student = $this->createUser('Estudiante', ['identification_card' => 20111222, 'email' => 'perdido@gabook.test']);

        $this->actingAs($admin, 'sanctum')->putJson("/api/v1/users/{$student->id}", [
            'name' => $student->name,
            'last_name' => $student->last_name,
            'identification_card' => 20111222,
            'email' => 'recuperado@gabook.test',
            'role' => 'Estudiante',
        ])->assertOk()->assertJsonPath('email', 'recuperado@gabook.test');
    }

    public function test_the_database_enforces_uniqueness(): void
    {
        $this->createUser('Asistente', ['identification_card' => 20111222]);

        $this->expectException(QueryException::class);
        DB::table('users')->insert([
            'name' => 'Directo', 'last_name' => 'BD', 'identification_card' => 20111222,
            'email' => 'directo@gabook.test', 'password' => 'x', 'status' => 'activo',
        ]);
    }

    public function test_duplicate_report_command_is_clean_on_a_consistent_database(): void
    {
        $this->createUser('Asistente');

        $this->artisan('users:duplicate-identification-cards')
            ->expectsOutput('No hay cédulas duplicadas.')
            ->assertSuccessful();
    }
}
