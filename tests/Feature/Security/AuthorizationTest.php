<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Hallazgos C5, C6 y C7: autorización por rol en el servidor, acceso a
 * expedientes ajenos (IDOR) y asignación masiva de atributos.
 */
class AuthorizationTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_student_cannot_open_user_management(): void
    {
        $student = $this->createUser('Estudiante');

        $this->actingAs($student)->get('/user')->assertForbidden();
        $this->actingAs($student)->get('/roles')->assertForbidden();
        $this->actingAs($student)->get('/lapso')->assertForbidden();
        $this->actingAs($student)->get('/reports')->assertForbidden();
    }

    public function test_coordinator_cannot_open_user_management_but_can_list_students(): void
    {
        $coordinator = $this->createUser('Coordinador');

        $this->actingAs($coordinator)->get('/user')->assertForbidden();
        $this->actingAs($coordinator)->get('/estudiantes')->assertOk();
    }

    public function test_admin_can_open_user_management(): void
    {
        $admin = $this->createUser('Administrador');

        $this->actingAs($admin)->get('/user')->assertOk();
        $this->actingAs($admin)->get('/user/create')->assertOk();
        $this->actingAs($admin)->get('/lapso')->assertOk();
    }

    /** FUNCTIONAL_SPECIFICATIONS.md §7: el Director supervisa pero no opera el día a día. */
    public function test_director_supervises_without_operating(): void
    {
        $director = $this->createUser('Director');
        $target = $this->createUser('Asistente');

        $this->actingAs($director)->get('/user')->assertOk();
        $this->actingAs($director)->get('/reports')->assertOk();
        $this->actingAs($director)->get('/user/create')->assertForbidden();
        $this->actingAs($director)->get("/user/{$target->id}/edit")->assertForbidden();
        $this->actingAs($director)->get('/lapso')->assertForbidden();
        $this->actingAs($director)->get('/roles')->assertForbidden();
    }

    /** El Asistente es operativo: no altera notas, no cierra lapsos ni gestiona usuarios. */
    public function test_assistant_cannot_grade_or_manage_lapses(): void
    {
        $assistant = $this->createUser('Asistente');
        $student = $this->createStudent();

        $this->actingAs($assistant)->get('/estudiantes')->assertOk();
        $this->actingAs($assistant)->get("/calificacion/estudiante/{$student->id}")->assertForbidden();
        $this->actingAs($assistant)->post("/store/calificacion/{$student->id}")->assertForbidden();
        $this->actingAs($assistant)->get('/lapso')->assertForbidden();
        $this->actingAs($assistant)->get('/user')->assertForbidden();
    }

    public function test_coordinator_only_reaches_students_of_own_program(): void
    {
        $coordinator = $this->createUser('Coordinador', ['doctorado_id' => 2]);
        $mine = $this->createStudent(['last_name' => 'DeCiencias', 'doctorado_id' => 2]);
        $both = $this->createStudent(['last_name' => 'DeAmbos', 'doctorado_id' => 3]);
        $other = $this->createStudent(['last_name' => 'DeGerencia', 'doctorado_id' => 1]);

        $this->actingAs($coordinator)->get('/estudiantes')
            ->assertOk()
            ->assertSee('DeCiencias')
            ->assertSee('DeAmbos')
            ->assertDontSee('DeGerencia');

        $this->actingAs($coordinator)->get("/estudiantes/{$mine->id}")->assertOk();
        $this->actingAs($coordinator)->get("/estudiantes/{$both->id}")->assertOk();
        $this->actingAs($coordinator)->get("/estudiantes/{$other->id}")->assertForbidden();
    }

    public function test_coordinator_cannot_edit_student_records(): void
    {
        $coordinator = $this->createUser('Coordinador', ['doctorado_id' => 3]);
        $student = $this->createStudent(['doctorado_id' => 1]);

        $this->actingAs($coordinator)->put("/estudiantes/{$student->id}", ['status' => '0'])->assertForbidden();
    }

    public function test_user_without_role_does_not_see_the_admin_dashboard(): void
    {
        $nobody = $this->createUser();

        $this->actingAs($nobody)->get('/home')
            ->assertOk()
            ->assertSee('no tiene un rol asignado')
            ->assertDontSee('resumen de tu institución');
    }

    public function test_student_cannot_open_another_students_record(): void
    {
        $me = $this->createStudent();
        $other = $this->createStudent();

        $this->actingAs($me->user)->get("/estudiantes/{$other->id}")->assertForbidden();
        $this->actingAs($me->user)->get("/documentEstudiante/{$other->id}")->assertForbidden();
        $this->actingAs($me->user)->get("/estudiantes/{$other->id}/edit")->assertForbidden();
    }

    public function test_student_can_open_own_document_form(): void
    {
        $me = $this->createStudent();

        $this->actingAs($me->user)->get("/documentEstudiante/{$me->id}")->assertOk();
    }

    public function test_student_cannot_grade_documents(): void
    {
        $me = $this->createStudent();

        $this->actingAs($me->user)->get("/calificacion/estudiante/{$me->id}")->assertForbidden();
        $this->actingAs($me->user)->post("/store/calificacion/{$me->id}")->assertForbidden();
    }

    public function test_student_update_cannot_target_another_user(): void
    {
        $me = $this->createStudent();
        $victim = $this->createUser('Director', ['name' => 'Original']);

        $this->actingAs($me->user)->put("/estudiantes/{$me->id}", [
            'user_id' => $victim->id,
            'first_name' => 'Hackeado',
        ]);

        $this->assertSame('Original', User::find($victim->id)->name);
        $this->assertSame($me->user_id, $me->fresh()->user_id);
    }

    public function test_user_update_ignores_non_whitelisted_attributes(): void
    {
        $admin = $this->createUser('Administrador');
        $target = $this->createUser('Asistente');

        $this->actingAs($admin)->put("/user/{$target->id}", $this->userPayload($target, [
            'name' => 'Nuevo',
            'api_token' => 'token-inyectado',
        ]))->assertRedirect(route('user.index'));

        $target->refresh();
        $this->assertSame('Nuevo', $target->name);
        $this->assertNull($target->api_token);
    }

    public function test_changing_the_role_replaces_the_previous_one(): void
    {
        $admin = $this->createUser('Administrador');
        $target = $this->createUser('Asistente');

        $this->actingAs($admin)->put("/user/{$target->id}", $this->userPayload($target, ['role' => 'Coordinador']));

        $target->refresh();
        $this->assertTrue($target->hasRole('Coordinador'));
        $this->assertFalse($target->hasRole('Asistente'));
    }

    public function test_duplicate_email_is_reported_on_the_email_field(): void
    {
        $admin = $this->createUser('Administrador');
        $taken = $this->createUser('Asistente', ['email' => 'ocupado@gabook.test']);
        $target = $this->createUser('Asistente');

        $this->actingAs($admin)->from("/user/{$target->id}/edit")
            ->put("/user/{$target->id}", $this->userPayload($target, ['email' => 'OCUPADO@gabook.test']))
            ->assertRedirect("/user/{$target->id}/edit")
            ->assertSessionHasErrors('email');

        $this->assertNotSame($taken->email, $target->fresh()->email);
    }

    public function test_role_update_errors_do_not_leak_internal_messages(): void
    {
        $admin = $this->createUser('Administrador');

        $response = $this->actingAs($admin)->putJson('/roles/99999', ['name' => 'Inexistente']);

        $response->assertNotFound()->assertJsonMissingPath('error');
        $this->assertStringNotContainsString('Exception', $response->getContent());
    }

    public function test_system_roles_cannot_be_deleted_through_the_api(): void
    {
        $admin = $this->createUser('Administrador');

        $this->actingAs($admin, 'sanctum')->deleteJson('/api/roles/1')
            ->assertStatus(422)
            ->assertJsonPath('errors.name.0', '«Director» es un rol del sistema: no se puede renombrar ni eliminar.');
    }

    private function userPayload(User $user, array $overrides): array
    {
        return array_merge([
            'name' => $user->name,
            'last_name' => $user->last_name,
            'identification_card' => $user->identification_card,
            'email' => $user->email,
            'role' => $user->roles->first()->name,
            'status' => 'activo',
        ], $overrides);
    }
}
