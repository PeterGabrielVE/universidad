<?php

namespace Tests\Feature\Characterization;

use App\Estudiante;
use App\Lapso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Inventario #9, #10 y #13: alta de usuarios desde el modal Blade, filtros por
 * rol, borrado, y listado/ficha/borrado de estudiantes.
 */
class UserAndStudentManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->admin = $this->createUser('Administrador');
        $this->actingAs($this->admin);
    }

    public function test_store_creates_staff_user_with_matching_role(): void
    {
        $this->from('/user')->post('/user', $this->newUserPayload(['role' => 'Coordinador']))->assertRedirect('/user');

        $user = User::where('email', 'nuevo@gabook.test')->firstOrFail();
        $this->assertTrue($user->hasRole('Coordinador'));
        $this->assertNull(Estudiante::where('user_id', $user->id)->first());
    }

    public function test_store_creates_student_record_for_student_role(): void
    {
        $this->post('/user', $this->newUserPayload(['role' => 'Estudiante']));

        $user = User::where('email', 'nuevo@gabook.test')->firstOrFail();
        $this->assertTrue($user->hasRole('Estudiante'));
        $this->assertNotNull(Estudiante::where('user_id', $user->id)->first());
    }

    public function test_store_hashes_the_password(): void
    {
        $this->post('/user', $this->newUserPayload(['role' => 'Asistente']));

        $this->post('/logout');
        $this->post('/login', ['email' => 'nuevo@gabook.test', 'password' => 'secreto123']);
        $this->assertAuthenticated();
    }

    /** Inventario #10 (corregido en la Fase 2): la vista pasa el rol y la API filtra por él. */
    public function test_role_filter_lists_only_that_role(): void
    {
        $this->createUser('Coordinador', ['last_name' => 'CoordinadorUno']);
        $this->createUser('Asistente', ['last_name' => 'AsistenteUno']);

        $this->get('/usuario/directivo')
            ->assertOk()
            ->assertSee('role-filter="Coordinador"', false);

        $this->getJson('/api/v1/users?role=Coordinador')
            ->assertOk()
            ->assertJsonFragment(['last_name' => 'CoordinadorUno', 'role' => 'Coordinador'])
            ->assertJsonMissing(['last_name' => 'AsistenteUno']);
    }

    public function test_api_search_matches_name_last_name_or_email(): void
    {
        $this->createUser('Asistente', ['last_name' => 'Buscable', 'email' => 'otra@gabook.test']);
        $this->createUser('Asistente', ['last_name' => 'Ajeno']);

        $this->getJson('/api/v1/users?search=buscable')
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['last_name' => 'Buscable', 'status' => 'activo']);
    }

    public function test_edit_page_opens_from_the_listing_link(): void
    {
        $target = $this->createUser('Asistente', ['last_name' => 'Editable']);

        $this->get("/user/{$target->id}/edit")->assertOk()->assertSee('Editable');
    }

    public function test_create_user_through_the_api_used_by_the_vue_form(): void
    {
        $this->postJson('/api/v1/users', $this->newUserPayload(['role' => 'Docente', 'status' => 'activo']))
            ->assertCreated()
            ->assertJsonFragment(['email' => 'nuevo@gabook.test', 'role' => 'Docente']);
    }

    public function test_api_rejects_duplicate_email_with_a_field_error(): void
    {
        $this->createUser('Asistente', ['email' => 'nuevo@gabook.test']);

        $this->postJson('/api/v1/users', $this->newUserPayload(['role' => 'Asistente']))
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_admin_deletes_a_user(): void
    {
        $target = $this->createUser('Asistente');

        $this->delete("/usuario/delete/{$target->id}")->assertRedirect();

        $this->assertModelMissing($target);
    }

    public function test_staff_lists_students(): void
    {
        $student = $this->createStudent(['last_name' => 'Expediente']);

        $this->get('/estudiantes')->assertOk();
        $this->assertNotNull($student->fresh());
    }

    public function test_staff_opens_a_student_record(): void
    {
        Lapso::factory()->create(['name' => 'Lapso Julio 2026-Diciembre 2026']);
        $student = $this->createStudent(['identification_card' => 24681357]);

        $this->get("/estudiantes/{$student->id}")
            ->assertOk()
            ->assertSee('24681357')
            ->assertSee('Lapso Julio 2026-Diciembre 2026');
    }

    /** Regresión: en una instalación nueva sin lapsos la ficha daba 500. */
    public function test_student_record_opens_before_any_lapse_exists(): void
    {
        $student = $this->createStudent();

        $this->get("/estudiantes/{$student->id}")->assertOk();
        $this->get("/estudiantes/{$student->id}/edit")->assertOk();
    }

    public function test_destroy_removes_student_and_user(): void
    {
        $student = $this->createStudent();
        $userId = $student->user_id;

        $this->delete("/estudiantes/{$student->id}")->assertRedirect();

        $this->assertModelMissing($student);
        $this->assertNull(User::find($userId));
    }

    private function newUserPayload(array $overrides): array
    {
        return array_merge([
            'name' => 'Nuevo',
            'last_name' => 'Usuario',
            'identification_card' => 30111222,
            'email' => 'nuevo@gabook.test',
            'password' => 'secreto123',
        ], $overrides);
    }
}
