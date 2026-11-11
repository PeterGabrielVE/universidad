<?php

namespace Tests\Feature\Security;

use App\User;
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
        $admin = $this->createUser('Director');

        $this->actingAs($admin)->get('/user')->assertOk();
        $this->actingAs($admin)->get('/lapso')->assertOk();
    }

    public function test_user_without_role_does_not_see_the_admin_dashboard(): void
    {
        $nobody = $this->createUser();

        $this->actingAs($nobody)->get('/home')->assertForbidden();
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

        $this->actingAs($admin)->put("/user/{$target->id}", [
            'name' => 'Nuevo',
            'rol_id' => 3,
            'api_token' => 'token-inyectado',
        ]);

        $target->refresh();
        $this->assertSame('Nuevo', $target->name);
        $this->assertNull($target->api_token);
    }

    public function test_changing_rol_id_keeps_spatie_role_in_sync(): void
    {
        $admin = $this->createUser('Administrador');
        $target = $this->createUser('Asistente');

        $this->actingAs($admin)->put("/user/{$target->id}", ['name' => $target->name, 'rol_id' => 2]);

        $target->refresh();
        $this->assertSame(2, (int) $target->rol_id);
        $this->assertTrue($target->hasRole('Coordinador'));
        $this->assertFalse($target->hasRole('Asistente'));
    }

    public function test_role_update_errors_do_not_leak_internal_messages(): void
    {
        $admin = $this->createUser('Director');

        $response = $this->actingAs($admin)->putJson('/roles/99999', [
            'name' => 'Inexistente',
            'guard_name' => 'web',
        ]);

        $response->assertJsonMissingPath('error');
        $this->assertStringNotContainsString('Role not found', $response->getContent());
    }
}
