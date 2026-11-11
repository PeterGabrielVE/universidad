<?php

namespace Tests\Feature\Academic;

use App\Doctorado;
use App\Estudiante;
use App\Lapso;
use App\Lapso_Estudiante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Inventario #16 (CRUD de lapsos) y #17 (cierre), FUNCTIONAL_SPECIFICATIONS.md §3, §7, §10.
 */
class LapseManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    private Doctorado $gerencia;
    private Doctorado $ciencias;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->gerencia = Doctorado::factory()->create(['id' => 1, 'name' => 'Doctorado en Gerencia', 'min_passing_grade' => 75]);
        $this->ciencias = Doctorado::factory()->create(['id' => 2, 'name' => 'Doctorado en Ciencias', 'min_passing_grade' => 48]);
    }

    public function test_admin_lists_lapses_with_program_and_status(): void
    {
        Lapso::factory()->create(['program_id' => $this->ciencias->id, 'name' => 'Lapso Enero 2026-Junio 2026']);

        $this->actingAs($this->admin())->get('/lapso')
            ->assertOk()
            ->assertSee('Lapso Enero 2026-Junio 2026')
            ->assertSee('Doctorado en Ciencias')
            ->assertSee('Abierto');
    }

    public function test_store_generates_the_name_and_redirects(): void
    {
        $this->actingAs($this->admin())->post('/lapso', [
            'program_id' => $this->ciencias->id,
            'start_lapse' => '2026-07-13',
            'end_lapse' => '2026-12-11',
            'status' => 'planificacion',
        ])->assertRedirect(route('lapso.index'));

        $this->assertDatabaseHas('lapses', [
            'program_id' => $this->ciencias->id,
            'name' => 'Lapso Julio 2026-Diciembre 2026',
            'start_lapse' => '2026-07-13',
            'status' => 'planificacion',
        ]);
    }

    public function test_store_rejects_end_before_start(): void
    {
        $this->actingAs($this->admin())->from('/lapso')->post('/lapso', [
            'program_id' => $this->ciencias->id,
            'start_lapse' => '2026-12-11',
            'end_lapse' => '2026-07-13',
            'status' => 'planificacion',
        ])->assertRedirect('/lapso')->assertSessionHasErrors('end_lapse');

        $this->assertDatabaseCount('lapses', 0);
    }

    public function test_store_cannot_create_a_closed_lapse(): void
    {
        $this->actingAs($this->admin())->post('/lapso', [
            'program_id' => $this->ciencias->id,
            'start_lapse' => '2026-07-13',
            'end_lapse' => '2026-12-11',
            'status' => 'cerrado',
        ])->assertSessionHasErrors('status');
    }

    public function test_edit_returns_the_lapse_as_json_for_the_modal(): void
    {
        $lapso = Lapso::factory()->create(['program_id' => $this->ciencias->id]);

        $this->actingAs($this->admin())->getJson("/editLapso/{$lapso->id}")
            ->assertOk()
            ->assertJson(['id' => $lapso->id, 'status' => 'abierto', 'program_id' => $this->ciencias->id]);
    }

    public function test_update_changes_dates_and_regenerates_the_name(): void
    {
        $lapso = Lapso::factory()->planned()->create(['program_id' => $this->ciencias->id]);

        $this->actingAs($this->admin())->postJson("/updateLapso/{$lapso->id}", [
            'start_lapse' => '2026-02-02',
            'end_lapse' => '2026-07-03',
            'status' => 'abierto',
        ])->assertOk()->assertJsonPath('data.name', 'Lapso Febrero 2026-Julio 2026');

        $this->assertDatabaseHas('lapses', ['id' => $lapso->id, 'start_lapse' => '2026-02-02', 'status' => 'abierto']);
    }

    /** Regresión #17: el cierre cambiaba las inscripciones de TODOS los lapsos. */
    public function test_closing_only_affects_active_enrollments_of_that_lapse(): void
    {
        $closing = Lapso::factory()->create(['program_id' => $this->ciencias->id]);
        $other = Lapso::factory()->create(['program_id' => $this->ciencias->id, 'name' => 'Otro']);
        $student = $this->createStudent();

        $passed = $this->enrollment($student, $closing, 80);
        $failed = $this->enrollment($student, $closing, 30);
        $frozen = $this->enrollment($student, $closing, null, 'congelado');
        $elsewhere = $this->enrollment($student, $other, 20);

        $this->closeLapse($closing)->assertOk();

        $this->assertEnrollment($passed, 'cerrado', 'aprobado');
        $this->assertEnrollment($failed, 'cerrado', 'reprobado');
        $this->assertEnrollment($frozen, 'congelado', null);
        $this->assertEnrollment($elsewhere, 'activo', null);
        $this->assertSame('cerrado', $closing->fresh()->status->value);
    }

    public function test_closing_uses_the_programs_minimum_grade(): void
    {
        $gerencia = Lapso::factory()->create(['program_id' => $this->gerencia->id]);
        $student = $this->createStudent();
        $enrollment = $this->enrollment($student, $gerencia, 60);

        $this->closeLapse($gerencia)->assertOk();

        $this->assertEnrollment($enrollment, 'cerrado', 'reprobado');
    }

    public function test_closing_is_refused_while_active_enrollments_lack_a_grade(): void
    {
        $lapso = Lapso::factory()->create(['program_id' => $this->ciencias->id]);
        $student = $this->createStudent();
        $graded = $this->enrollment($student, $lapso, 90);
        $this->enrollment($student, $lapso, null);

        $this->closeLapse($lapso)
            ->assertStatus(422)
            ->assertJsonPath('errors.status.0', 'No se puede cerrar «Lapso Enero 2026-Junio 2026»: 1 inscripción(es) activa(s) sin nota.');

        $this->assertSame('abierto', $lapso->fresh()->status->value);
        $this->assertEnrollment($graded, 'activo', null);
    }

    public function test_a_closed_lapse_cannot_be_modified_or_deleted(): void
    {
        $lapso = Lapso::factory()->closed()->create(['program_id' => $this->ciencias->id]);

        $this->actingAs($this->admin())->postJson("/updateLapso/{$lapso->id}", [
            'start_lapse' => '2026-01-12', 'end_lapse' => '2026-06-30', 'status' => 'cerrado',
        ])->assertStatus(422);

        $this->actingAs($this->admin())->delete("/lapso/{$lapso->id}")->assertSessionHasErrors();
        $this->assertModelExists($lapso);
    }

    public function test_only_a_planned_lapse_can_be_deleted(): void
    {
        $planned = Lapso::factory()->planned()->create(['program_id' => $this->ciencias->id]);
        $open = Lapso::factory()->create(['program_id' => $this->ciencias->id]);

        $this->actingAs($this->admin())->delete("/lapso/{$planned->id}")->assertRedirect(route('lapso.index'));
        $this->actingAs($this->admin())->delete("/lapso/{$open->id}")->assertSessionHasErrors();

        $this->assertModelMissing($planned);
        $this->assertModelExists($open);
    }

    public function test_coordinator_sees_and_manages_only_own_program_lapses(): void
    {
        $coordinator = $this->createUser('Coordinador', ['programs' => [$this->ciencias->id]]);
        $mine = Lapso::factory()->create(['program_id' => $this->ciencias->id, 'name' => 'Lapso de Ciencias']);
        $theirs = Lapso::factory()->create(['program_id' => $this->gerencia->id, 'name' => 'Lapso de Gerencia']);

        $this->actingAs($coordinator)->get('/lapso')
            ->assertOk()
            ->assertSee('Lapso de Ciencias')
            ->assertDontSee('Lapso de Gerencia');

        $this->actingAs($coordinator)->getJson("/editLapso/{$mine->id}")->assertOk();
        $this->actingAs($coordinator)->getJson("/editLapso/{$theirs->id}")->assertForbidden();
        $this->actingAs($coordinator)->postJson("/updateLapso/{$theirs->id}", [
            'start_lapse' => '2026-01-12', 'end_lapse' => '2026-06-26', 'status' => 'cerrado',
        ])->assertForbidden();
        $this->assertSame('abierto', $theirs->fresh()->status->value);
    }

    public function test_coordinator_cannot_create_lapses_in_other_programs(): void
    {
        $coordinator = $this->createUser('Coordinador', ['programs' => [$this->ciencias->id]]);

        $this->actingAs($coordinator)->post('/lapso', [
            'program_id' => $this->gerencia->id,
            'start_lapse' => '2027-01-11',
            'end_lapse' => '2027-06-25',
            'status' => 'planificacion',
        ])->assertForbidden();

        $this->actingAs($coordinator)->post('/lapso', [
            'program_id' => $this->ciencias->id,
            'start_lapse' => '2027-01-11',
            'end_lapse' => '2027-06-25',
            'status' => 'planificacion',
        ])->assertRedirect(route('lapso.index'));

        $this->assertDatabaseHas('lapses', ['program_id' => $this->ciencias->id, 'name' => 'Lapso Enero 2027-Junio 2027']);
        $this->assertDatabaseMissing('lapses', ['program_id' => $this->gerencia->id]);
    }

    public function test_director_and_assistant_cannot_manage_lapses(): void
    {
        $this->actingAs($this->createUser('Director'))->get('/lapso')->assertForbidden();
        $this->actingAs($this->createUser('Asistente'))->get('/lapso')->assertForbidden();
    }

    private function admin(): User
    {
        return $this->createUser('Administrador');
    }

    private function closeLapse(Lapso $lapso)
    {
        return $this->actingAs($this->admin())->postJson("/updateLapso/{$lapso->id}", [
            'start_lapse' => '2026-01-12',
            'end_lapse' => '2026-06-26',
            'status' => 'cerrado',
        ]);
    }

    private function enrollment(Estudiante $student, Lapso $lapso, ?float $note, string $status = 'activo'): int
    {
        return Lapso_Estudiante::query()->insertGetId([
            'student_id' => $student->id,
            'lapse_id' => $lapso->id,
            'note' => $note,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function assertEnrollment(int $id, string $status, ?string $result): void
    {
        $this->assertDatabaseHas('lapses_students', ['id' => $id, 'status' => $status, 'result' => $result]);
    }
}
