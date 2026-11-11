<?php

namespace Tests\Feature\Academic;

use App\Doctorado;
use App\Estudiante;
use App\Lapso;
use App\Models\User;
use App\Pais;
use App\Sede;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Expediente del estudiante: #14 (edición) y #15 (alta), FUNCTIONAL_SPECIFICATIONS.md §19.
 */
class StudentRecordTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    private Doctorado $program;
    private Estudiante $student;
    private Pais $country;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->program = Doctorado::factory()->create();
        $this->country = Pais::create(['code' => 'VE', 'name' => 'Venezuela']);
        $this->student = $this->createStudent([
            'name' => 'María', 'last_name' => 'González', 'identification_card' => 20111222,
            'email' => 'maria@gabook.test', 'programs' => [$this->program->id],
        ]);
    }

    public function test_student_edits_only_contact_data_and_identity_stays_intact(): void
    {
        $other = Doctorado::factory()->create();
        $lapse = Lapso::factory()->create(['program_id' => $this->program->id]);

        $this->actingAs($this->student->user)->put("/estudiantes/{$this->student->id}", [
            'phone' => '+58 414 1234567',
            'country_id' => $this->country->id,
            // Campos que el estudiante no puede cambiar: se ignoran.
            'first_name' => 'Hackeada',
            'identification_card' => 99999999,
            'email' => 'otra@gabook.test',
            'doctorado_id' => $other->id,
            'admission_lapse_id' => $lapse->id,
            'equivalency' => 1,
        ])->assertRedirect(route('estudiantes.show', $this->student->id));

        $user = $this->student->user->fresh();
        $this->assertSame('+58 414 1234567', $user->phone);
        $this->assertSame($this->country->id, (int) $user->country_id);
        $this->assertSame('María', $user->name);
        $this->assertSame(20111222, (int) $user->identification_card);
        $this->assertSame('maria@gabook.test', $user->email);
        $this->assertSame([$this->program->id], $user->programs()->pluck('doctorados.id')->all());
        $this->assertNull($this->student->fresh()->lapse_id);
    }

    public function test_student_sees_own_record_with_contact_form_only(): void
    {
        $this->actingAs($this->student->user)->get("/estudiantes/{$this->student->id}")
            ->assertOk()
            ->assertSee('Mis datos de contacto')
            ->assertSee('solicítalo a Control de Estudios')
            ->assertDontSee('Lapso de ingreso</label>', false);
    }

    public function test_invalid_phone_is_reported_on_its_field(): void
    {
        $this->actingAs($this->student->user)->from("/estudiantes/{$this->student->id}")
            ->put("/estudiantes/{$this->student->id}", ['phone' => 'llámame'])
            ->assertRedirect("/estudiantes/{$this->student->id}")
            ->assertSessionHasErrors('phone');
    }

    public function test_assistant_edits_administrative_data(): void
    {
        $campus = Sede::create(['name' => 'Sede Central']);
        $lapse = Lapso::factory()->create(['program_id' => $this->program->id, 'name' => 'Lapso de Ingreso Demo']);

        $this->actingAs($this->createUser('Asistente'))->put("/estudiantes/{$this->student->id}", [
            'phone' => '0414 1234567',
            'country_id' => $this->country->id,
            'sede_id' => $campus->id,
            'admission_lapse_id' => $lapse->id,
            'equivalency' => 1,
        ])->assertRedirect(route('estudiantes.show', $this->student->id));

        $student = $this->student->fresh();
        $this->assertSame($lapse->id, (int) $student->lapse_id);
        $this->assertSame('Sí', $student->equivalency);
        $this->assertSame($campus->id, (int) $student->user->sede_id);

        $this->actingAs($this->createUser('Administrador'))->get("/estudiantes/{$this->student->id}")
            ->assertOk()->assertSee('Lapso de Ingreso Demo')->assertSee('Sede Central')->assertSee('Identidad y programas');
    }

    public function test_admission_lapse_of_another_program_is_rejected(): void
    {
        $foreign = Lapso::factory()->create(['program_id' => Doctorado::factory()->create()->id]);

        $this->actingAs($this->createUser('Administrador'))->put("/estudiantes/{$this->student->id}", [
            'admission_lapse_id' => $foreign->id,
        ])->assertSessionHasErrors('admission_lapse_id');
    }

    public function test_read_only_roles_cannot_edit_records(): void
    {
        foreach (['Director', 'Coordinador'] as $role) {
            $staff = $this->createUser($role, ['programs' => [$this->program->id]]);

            $this->actingAs($staff)->get("/estudiantes/{$this->student->id}")->assertOk()->assertDontSee('Guardar');
            $this->actingAs($staff)->put("/estudiantes/{$this->student->id}", ['phone' => '04140000000'])->assertForbidden();
        }
    }

    /** #15: toda cuenta Estudiante tiene expediente; si faltara, se crea al entrar. */
    public function test_student_without_record_gets_one(): void
    {
        $user = $this->createUser('Estudiante');
        $this->assertNull(Estudiante::where('user_id', $user->id)->first());

        $this->actingAs($user)->get('/home')->assertRedirect(route('estudiantes.create'));
        $response = $this->actingAs($user)->get('/estudiantes/create');

        $record = Estudiante::where('user_id', $user->id)->firstOrFail();
        $response->assertRedirect(route('estudiantes.show', $record->id));
    }

    public function test_promoting_an_applicant_to_student_creates_the_record(): void
    {
        $applicant = $this->createUser('Aspirante');

        $this->actingAs($this->createUser('Administrador'))->put("/user/{$applicant->id}", [
            'name' => $applicant->name,
            'last_name' => $applicant->last_name,
            'identification_card' => $applicant->identification_card,
            'email' => $applicant->email,
            'role' => 'Estudiante',
            'program_ids' => [$this->program->id],
        ])->assertRedirect(route('user.index'));

        $this->assertNotNull(Estudiante::where('user_id', $applicant->id)->first());
    }

    /** Rutas que siempre daban 500 (vista inexistente) y se retiraron en la Fase 3d. */
    public function test_retired_broken_routes_no_longer_exist(): void
    {
        $admin = $this->createUser('Administrador');

        $this->actingAs($admin)->get('/getEstudiante/1')->assertNotFound();
        $this->actingAs($admin)->get('/crearEstudiante/123')->assertNotFound();
        $this->actingAs($admin)->post('/buscar.estudiante', ['consulta' => 1])->assertNotFound();
    }
}
