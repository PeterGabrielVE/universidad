<?php

namespace Tests\Feature\Characterization;

use App\Estudiante;
use App\Lapso;
use App\User;
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
        $this->from('/user')->post('/user', $this->newUserPayload(['rol_id' => 2]))->assertRedirect('/user');

        $user = User::where('email', 'nuevo@gabook.test')->firstOrFail();
        $this->assertTrue($user->hasRole('Coordinador'));
        $this->assertNull(Estudiante::where('user_id', $user->id)->first());
    }

    public function test_store_creates_student_record_for_student_role(): void
    {
        $this->post('/user', $this->newUserPayload(['rol_id' => 4]));

        $user = User::where('email', 'nuevo@gabook.test')->firstOrFail();
        $this->assertTrue($user->hasRole('Estudiante'));
        $this->assertNotNull(Estudiante::where('user_id', $user->id)->first());
    }

    public function test_store_hashes_the_password(): void
    {
        $this->post('/user', $this->newUserPayload(['rol_id' => 3]));

        $this->post('/logout');
        $this->post('/login', ['email' => 'nuevo@gabook.test', 'password' => 'secreto123']);
        $this->assertAuthenticated();
    }

    public function test_role_filter_lists_only_that_role(): void
    {
        $this->markTestIncomplete(
            'Defecto conocido (inventario #10, PROJECT_STATUS.md): la vista monta <users-index>, '
            . 'que carga todos los usuarios por API e ignora el filtro. Se corrige en la Fase 2.'
        );

        $this->createUser('Coordinador', ['last_name' => 'CoordinadorUno']);
        $this->createUser('Asistente', ['last_name' => 'AsistenteUno']);

        $this->get('/usuario/directivo')
            ->assertOk()
            ->assertSee('CoordinadorUno')
            ->assertDontSee('AsistenteUno');
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
