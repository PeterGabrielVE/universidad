<?php

namespace Tests\Feature\Academic;

use App\Asignatura;
use App\Doctorado;
use App\Estudiante;
use App\Lapso;
use App\Lapso_Estudiante;
use App\Models\Section;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Flujo completo de la Fase 3c (FUNCTIONAL_SPECIFICATIONS.md §15, §17):
 * sección con docente titular → inscripción → acta → cierre del lapso, cierre
 * forzoso y corrección auditada; kardex.
 */
class GradeSheetFlowTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    private Doctorado $program;
    private Lapso $lapse;
    private Asignatura $course;
    private User $admin;
    private User $coordinator;
    private User $assistant;
    private User $teacher;
    private Estudiante $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();

        $this->program = Doctorado::factory()->create(['min_passing_grade' => 75]);
        DB::table('semester')->insertOrIgnore(['id' => 1, 'code' => 'I']);
        $this->course = Asignatura::create(['cod' => 'M1', 'name' => 'Metodología I', 'uc' => '3', 'semester_id' => 1, 'required' => '']);
        $this->program->courses()->attach($this->course->id);
        $this->lapse = Lapso::factory()->create(['program_id' => $this->program->id]);

        $this->admin = $this->createUser('Administrador');
        $this->coordinator = $this->createUser('Coordinador', ['programs' => [$this->program->id]]);
        $this->assistant = $this->createUser('Asistente');
        $this->teacher = $this->createUser('Docente', ['last_name' => 'Titular']);
        $this->student = $this->createStudent(['last_name' => 'Inscrita', 'programs' => [$this->program->id]]);
    }

    public function test_complete_flow_from_section_to_closed_lapse_and_kardex(): void
    {
        // 1. El Coordinador abre la sección y asigna el docente titular.
        $this->actingAs($this->coordinator)->post("/lapso/{$this->lapse->id}/secciones", [
            'course_id' => $this->course->id, 'code' => 'a', 'teacher_id' => $this->teacher->id,
        ])->assertRedirect(route('secciones.index', $this->lapse->id));
        $section = Section::firstOrFail();
        $this->assertSame('A', $section->code);

        // 2. El Asistente inscribe (preinscrito) y confirma (activo).
        $this->actingAs($this->assistant)->post("/secciones/{$section->id}/inscripciones", ['student_ids' => [$this->student->id]])
            ->assertRedirect(route('secciones.show', $section->id));
        $enrollment = Lapso_Estudiante::firstOrFail();
        $this->assertSame('pre_inscrito', $enrollment->status->value);
        $this->actingAs($this->assistant)->post("/inscripciones/{$enrollment->id}/confirmar")->assertRedirect();
        $this->assertSame('activo', $enrollment->fresh()->status->value);

        // 3. El lapso no se cierra con el acta abierta.
        $this->closeLapse()->assertStatus(422)->assertJsonPath('errors.status.0', fn ($m) => str_contains($m, '1 acta(s) siguen abiertas'));

        // 4. El docente registra la nota y cierra el acta.
        $this->actingAs($this->teacher)->get("/mis-secciones/{$section->id}/acta")->assertOk()->assertSee('Inscrita');
        $this->actingAs($this->teacher)->post("/mis-secciones/{$section->id}/notas", ['grades' => [$enrollment->id => 82]])
            ->assertRedirect(route('docente.acta', $section->id));
        $this->actingAs($this->teacher)->post("/mis-secciones/{$section->id}/cerrar", ['confirm' => '1'])
            ->assertRedirect(route('docente.acta', $section->id));
        $this->assertSame('cerrada', $section->fresh()->status->value);

        // 5. Con el acta cerrada el docente ya no edita.
        $this->actingAs($this->teacher)->post("/mis-secciones/{$section->id}/notas", ['grades' => [$enrollment->id => 99]])
            ->assertSessionHasErrors();
        $this->assertSame(82.0, (float) $enrollment->fresh()->note);

        // 6. El Coordinador cierra el lapso; el resultado usa la mínima del programa (75).
        $this->closeLapse()->assertOk();
        $this->assertSame('aprobado', $enrollment->fresh()->result->value);
        $this->assertSame(75.0, $this->lapse->fresh()->passing_grade_applied);

        // 7. El estudiante ve su kardex; otro estudiante no.
        $this->actingAs($this->student->user)->get("/estudiantes/{$this->student->id}/kardex")
            ->assertOk()->assertSee('Metodología I')->assertSee('82.00/100')->assertSee('Aprobado');
        $other = $this->createStudent(['programs' => [$this->program->id]]);
        $this->actingAs($other->user)->get("/estudiantes/{$this->student->id}/kardex")->assertForbidden();
    }

    public function test_only_the_lead_teacher_operates_the_sheet(): void
    {
        [$section, $enrollment] = $this->sectionWithActiveStudent();
        $intruder = $this->createUser('Docente');

        $this->actingAs($intruder)->get("/mis-secciones/{$section->id}/acta")->assertForbidden();
        $this->actingAs($intruder)->post("/mis-secciones/{$section->id}/notas", ['grades' => [$enrollment->id => 10]])->assertForbidden();
        $this->actingAs($this->assistant)->post("/mis-secciones/{$section->id}/notas", ['grades' => [$enrollment->id => 10]])->assertForbidden();
        $this->assertNull($enrollment->fresh()->note);
    }

    public function test_teacher_cannot_close_with_missing_grades(): void
    {
        [$section] = $this->sectionWithActiveStudent();

        $this->actingAs($this->teacher)->post("/mis-secciones/{$section->id}/cerrar", ['confirm' => '1'])->assertSessionHasErrors();
        $this->assertSame('abierta', $section->fresh()->status->value);
    }

    public function test_forced_closure_by_administrator_unblocks_the_lapse(): void
    {
        [$section, $enrollment] = $this->sectionWithActiveStudent();

        $this->actingAs($this->coordinator)->post("/secciones/{$section->id}/cierre-forzoso", ['confirm' => '1'])->assertForbidden();
        $this->actingAs($this->admin)->post("/secciones/{$section->id}/cierre-forzoso", ['confirm' => '1'])
            ->assertRedirect(route('secciones.show', $section->id));

        $section->refresh();
        $this->assertTrue($section->closed_forcibly);
        $this->assertSame($this->admin->id, $section->closed_by);

        // La nota pendiente la completa Control de Estudios con justificación.
        $this->actingAs($this->admin)->post("/inscripciones/{$enrollment->id}/correccion", [
            'note' => 60, 'justification' => 'Acta entregada en físico por el docente',
        ])->assertRedirect();

        $this->closeLapse()->assertOk();
        $this->assertSame('reprobado', $enrollment->fresh()->result->value);
        $this->actingAs($this->admin)->get("/secciones/{$section->id}")
            ->assertOk()->assertSee('Cierre administrativo forzoso')->assertSee('Acta entregada en físico por el docente');
    }

    public function test_correction_requires_justification_and_writes_the_audit_log(): void
    {
        [$section, $enrollment] = $this->sectionWithActiveStudent();
        $this->actingAs($this->teacher)->post("/mis-secciones/{$section->id}/notas", ['grades' => [$enrollment->id => 70]]);
        $this->actingAs($this->teacher)->post("/mis-secciones/{$section->id}/cerrar", ['confirm' => '1']);
        $this->closeLapse()->assertOk();
        $this->assertSame('reprobado', $enrollment->fresh()->result->value);

        $this->actingAs($this->admin)->post("/inscripciones/{$enrollment->id}/correccion", ['note' => 80])
            ->assertSessionHasErrors('justification');
        $this->actingAs($this->coordinator)->post("/inscripciones/{$enrollment->id}/correccion", ['note' => 80, 'justification' => 'x'])
            ->assertForbidden();

        $this->actingAs($this->admin)->post("/inscripciones/{$enrollment->id}/correccion", [
            'note' => 80, 'justification' => 'Resolución de apelación N° 123',
        ])->assertRedirect();

        $this->assertSame('aprobado', $enrollment->fresh()->result->value);
        $this->assertDatabaseHas('grade_audit_logs', [
            'enrollment_id' => $enrollment->id,
            'old_note' => 70,
            'new_note' => 80,
            'old_result' => 'reprobado',
            'new_result' => 'aprobado',
            'justification' => 'Resolución de apelación N° 123',
            'changed_by' => $this->admin->id,
        ]);
    }

    public function test_the_audit_justification_column_is_not_nullable(): void
    {
        [, $enrollment] = $this->sectionWithActiveStudent();

        $this->expectException(\Illuminate\Database\QueryException::class);
        DB::table('grade_audit_logs')->insert([
            'enrollment_id' => $enrollment->id, 'new_note' => 50, 'justification' => null, 'changed_by_name' => 'X',
        ]);
    }

    public function test_coordinator_of_another_program_cannot_touch_the_section(): void
    {
        [$section] = $this->sectionWithActiveStudent();
        $outsider = $this->createUser('Coordinador', ['programs' => [Doctorado::factory()->create()->id]]);

        $this->actingAs($outsider)->get("/secciones/{$section->id}")->assertForbidden();
        $this->actingAs($outsider)->get("/lapso/{$this->lapse->id}/secciones")->assertForbidden();
        $this->actingAs($outsider)->post("/lapso/{$this->lapse->id}/secciones", [
            'course_id' => $this->course->id, 'code' => 'B', 'teacher_id' => $this->teacher->id,
        ])->assertForbidden();
    }

    public function test_section_requires_an_active_teacher_and_a_pensum_course(): void
    {
        $other = Asignatura::create(['cod' => 'X9', 'name' => 'Fuera del pensum', 'uc' => '2', 'semester_id' => 1, 'required' => '']);

        $this->actingAs($this->admin)->post("/lapso/{$this->lapse->id}/secciones", [
            'course_id' => $other->id, 'code' => 'A', 'teacher_id' => $this->teacher->id,
        ])->assertSessionHasErrors('course_id');

        $this->actingAs($this->admin)->post("/lapso/{$this->lapse->id}/secciones", [
            'course_id' => $this->course->id, 'code' => 'A', 'teacher_id' => $this->assistant->id,
        ])->assertSessionHasErrors('teacher_id');
    }

    public function test_teacher_sees_own_sections(): void
    {
        $this->sectionWithActiveStudent();

        $this->actingAs($this->teacher)->get('/mis-secciones')->assertOk()->assertSee('Metodología I');
        $this->actingAs($this->createUser('Docente'))->get('/mis-secciones')->assertOk()->assertDontSee('Metodología I');
    }

    /** @return array{0: Section, 1: Lapso_Estudiante} */
    private function sectionWithActiveStudent(): array
    {
        $this->actingAs($this->admin)->post("/lapso/{$this->lapse->id}/secciones", [
            'course_id' => $this->course->id, 'code' => 'A', 'teacher_id' => $this->teacher->id,
        ]);
        $section = Section::firstOrFail();
        $this->actingAs($this->admin)->post("/secciones/{$section->id}/inscripciones", ['student_ids' => [$this->student->id]]);
        $enrollment = Lapso_Estudiante::firstOrFail();
        $this->actingAs($this->admin)->post("/inscripciones/{$enrollment->id}/confirmar");

        return [$section, $enrollment->fresh()];
    }

    private function closeLapse()
    {
        return $this->actingAs($this->coordinator)->postJson("/updateLapso/{$this->lapse->id}", [
            'start_lapse' => '2026-01-12', 'end_lapse' => '2026-06-26', 'status' => 'cerrado',
        ]);
    }
}
