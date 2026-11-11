<?php

namespace Tests\Feature\Academic;

use App\Asignatura;
use App\Doctorado;
use App\Lapso;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Facultades, programas, pensum N:M y relación usuario ↔ programas (§13, §15).
 */
class ProgramManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    private Faculty $faculty;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->faculty = Faculty::create(['name' => 'Facultad de Ciencias Económicas y Sociales']);
    }

    public function test_admin_creates_a_new_program_without_the_old_id_3_conflict(): void
    {
        Doctorado::factory()->create(['id' => 1, 'faculty_id' => $this->faculty->id]);
        Doctorado::factory()->create(['id' => 2, 'faculty_id' => $this->faculty->id]);

        $this->actingAs($this->admin())->post('/programas', [
            'name' => 'Maestría en Finanzas',
            'faculty_id' => $this->faculty->id,
            'min_passing_grade' => 60,
        ])->assertRedirect(route('programas.index'));

        $program = Doctorado::where('name', 'Maestría en Finanzas')->firstOrFail();
        $this->assertSame(60.0, $program->min_passing_grade);
        // Puede recibir el id 3 sin que nadie lo interprete como "ambos programas".
        $this->assertSame([], DB::table('program_user')->where('program_id', $program->id)->pluck('user_id')->all());
    }

    public function test_program_validation_reports_the_field(): void
    {
        Doctorado::factory()->create(['name' => 'Doctorado en Gerencia', 'faculty_id' => $this->faculty->id]);

        $this->actingAs($this->admin())->from('/programas')->post('/programas', [
            'name' => 'doctorado en gerencia',
            'faculty_id' => $this->faculty->id,
            'min_passing_grade' => 75,
        ])->assertRedirect('/programas')->assertSessionHasErrors('name');

        $this->actingAs($this->admin())->post('/programas', [
            'name' => 'Otro', 'faculty_id' => $this->faculty->id, 'min_passing_grade' => 120,
        ])->assertSessionHasErrors('min_passing_grade');
    }

    public function test_admin_updates_the_minimum_grade(): void
    {
        $program = Doctorado::factory()->create(['faculty_id' => $this->faculty->id]);

        $this->actingAs($this->admin())->put("/programas/{$program->id}", [
            'name' => $program->name,
            'faculty_id' => $this->faculty->id,
            'min_passing_grade' => 75,
            'description' => 'Exigencia doctoral',
        ])->assertRedirect(route('programas.index'));

        $this->assertSame(75.0, $program->fresh()->min_passing_grade);
    }

    public function test_a_program_in_use_is_not_deleted(): void
    {
        $program = Doctorado::factory()->create(['faculty_id' => $this->faculty->id]);
        Lapso::factory()->create(['program_id' => $program->id]);

        $this->actingAs($this->admin())->delete("/programas/{$program->id}")->assertSessionHasErrors();

        $this->assertModelExists($program);
    }

    public function test_admin_manages_faculties(): void
    {
        $this->actingAs($this->admin())->post('/facultades', ['name' => 'Facultad de Humanidades'])
            ->assertRedirect(route('facultades.index'));
        $created = Faculty::where('name', 'Facultad de Humanidades')->firstOrFail();

        Doctorado::factory()->create(['faculty_id' => $this->faculty->id]);
        $this->actingAs($this->admin())->delete("/facultades/{$this->faculty->id}")->assertSessionHasErrors();
        $this->actingAs($this->admin())->delete("/facultades/{$created->id}")->assertRedirect(route('facultades.index'));

        $this->assertModelExists($this->faculty);
        $this->assertModelMissing($created);
    }

    public function test_a_transversal_course_is_shared_by_two_pensums(): void
    {
        $gerencia = Doctorado::factory()->create(['faculty_id' => $this->faculty->id]);
        $ciencias = Doctorado::factory()->create(['faculty_id' => $this->faculty->id]);
        $metodologia = $this->course('M1', 'Metodología de la Investigación I');

        foreach ([$gerencia, $ciencias] as $program) {
            $this->actingAs($this->admin())->post("/programas/{$program->id}/pensum", ['course_ids' => [$metodologia->id]])
                ->assertRedirect(route('programas.pensum', $program->id));
        }

        $this->assertSame(1, Asignatura::where('cod', 'M1')->count());
        $this->assertEqualsCanonicalizing([$gerencia->id, $ciencias->id], $metodologia->programs()->pluck('doctorados.id')->all());

        $this->actingAs($this->admin())->get("/programas/{$gerencia->id}/pensum")
            ->assertOk()
            ->assertSee('Metodología de la Investigación I')
            ->assertSee('Sí (2 programas)');

        $this->actingAs($this->admin())->delete("/programas/{$gerencia->id}/pensum/{$metodologia->id}");
        $this->assertSame([$ciencias->id], $metodologia->programs()->pluck('doctorados.id')->all());
    }

    public function test_coordinator_consults_only_own_programs_and_cannot_edit(): void
    {
        $mine = Doctorado::factory()->create(['name' => 'Programa Propio', 'faculty_id' => $this->faculty->id]);
        $other = Doctorado::factory()->create(['name' => 'Programa Ajeno', 'faculty_id' => $this->faculty->id]);
        $coordinator = $this->createUser('Coordinador', ['programs' => [$mine->id]]);

        $this->actingAs($coordinator)->get('/programas')
            ->assertOk()
            ->assertSee('Programa Propio')
            ->assertDontSee('Programa Ajeno')
            ->assertDontSee('Nuevo programa');

        $this->actingAs($coordinator)->get("/programas/{$mine->id}/pensum")->assertOk()->assertDontSee('Añadir asignaturas');
        $this->actingAs($coordinator)->get("/programas/{$other->id}/pensum")->assertForbidden();
        $this->actingAs($coordinator)->post('/programas', ['name' => 'X', 'faculty_id' => $this->faculty->id, 'min_passing_grade' => 50])
            ->assertForbidden();
    }

    public function test_assistant_has_no_access_to_programs(): void
    {
        $this->actingAs($this->createUser('Asistente'))->get('/programas')->assertForbidden();
    }

    public function test_coordinator_of_several_programs_manages_lapses_of_each(): void
    {
        $a = Doctorado::factory()->create(['faculty_id' => $this->faculty->id]);
        $b = Doctorado::factory()->create(['faculty_id' => $this->faculty->id]);
        $c = Doctorado::factory()->create(['faculty_id' => $this->faculty->id]);
        $coordinator = $this->createUser('Coordinador', ['programs' => [$a->id, $b->id]]);

        foreach ([$a, $b] as $program) {
            $this->actingAs($coordinator)->post('/lapso', [
                'program_id' => $program->id, 'start_lapse' => '2027-01-11', 'end_lapse' => '2027-06-25', 'status' => 'planificacion',
            ])->assertRedirect(route('lapso.index'));
        }
        $this->actingAs($coordinator)->post('/lapso', [
            'program_id' => $c->id, 'start_lapse' => '2027-01-11', 'end_lapse' => '2027-06-25', 'status' => 'planificacion',
        ])->assertForbidden();
    }

    public function test_admin_assigns_several_programs_to_a_user(): void
    {
        $a = Doctorado::factory()->create(['faculty_id' => $this->faculty->id]);
        $b = Doctorado::factory()->create(['faculty_id' => $this->faculty->id]);
        $student = $this->createUser('Estudiante', ['programs' => [$a->id]]);

        $this->actingAs($this->admin())->put("/user/{$student->id}", [
            'name' => $student->name,
            'last_name' => $student->last_name,
            'identification_card' => $student->identification_card,
            'email' => $student->email,
            'role' => 'Estudiante',
            'program_ids' => [$a->id, $b->id],
        ])->assertRedirect(route('user.index'));

        $this->assertEqualsCanonicalizing([$a->id, $b->id], $student->programs()->pluck('doctorados.id')->all());
    }

    private function admin(): User
    {
        return $this->createUser('Administrador');
    }

    private function course(string $code, string $name): Asignatura
    {
        DB::table('semester')->insertOrIgnore(['id' => 1, 'code' => 'I']);

        return Asignatura::create(['cod' => $code, 'name' => $name, 'uc' => '3', 'semester_id' => 1, 'required' => '']);
    }
}
