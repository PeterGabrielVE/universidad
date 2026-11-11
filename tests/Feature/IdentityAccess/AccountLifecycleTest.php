<?php

namespace Tests\Feature\IdentityAccess;

use App\Doctorado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Registro público como Aspirante (§5) y estados de cuenta (§10).
 */
class AccountLifecycleTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_public_registration_creates_an_active_applicant(): void
    {
        $this->post('/register', [
            'name' => 'Rosa',
            'last_name' => 'Mendoza',
            'identification_card' => 25123456,
            'email' => 'Rosa@Gabook.test',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])->assertRedirect('/home');

        $user = User::where('email', 'rosa@gabook.test')->firstOrFail();
        $this->assertTrue($user->hasRole('Aspirante'));
        $this->assertSame('activo', $user->status->value);
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_cannot_choose_a_role(): void
    {
        $this->post('/register', [
            'name' => 'Intruso',
            'last_name' => 'Prueba',
            'identification_card' => 25000001,
            'email' => 'intruso@gabook.test',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
            'role' => 'Administrador',
        ]);

        $user = User::where('email', 'intruso@gabook.test')->firstOrFail();
        $this->assertSame(['Aspirante'], $user->getRoleNames()->all());
    }

    public function test_registration_requires_last_name_and_identification_card(): void
    {
        $this->post('/register', [
            'name' => 'Sin',
            'email' => 'incompleto@gabook.test',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])->assertSessionHasErrors(['last_name', 'identification_card']);

        $this->assertDatabaseMissing('users', ['email' => 'incompleto@gabook.test']);
    }

    public function test_registration_rejects_an_email_already_in_use(): void
    {
        $this->createUser('Asistente', ['email' => 'ocupado@gabook.test']);

        $this->from('/register')->post('/register', [
            'name' => 'Otra',
            'last_name' => 'Persona',
            'identification_card' => 25000002,
            'email' => 'ocupado@gabook.test',
            'password' => 'secreto123',
            'password_confirmation' => 'secreto123',
        ])->assertRedirect('/register')->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_applicant_home_shows_the_academic_offer(): void
    {
        Doctorado::create(['name' => 'Doctorado en Gerencia Avanzada', 'description' => 'Gestión']);
        $applicant = $this->createUser('Aspirante');

        $this->actingAs($applicant)->get('/home')
            ->assertOk()
            ->assertSee('Oferta académica')
            ->assertSee('Doctorado en Gerencia Avanzada')
            ->assertDontSee('resumen de tu institución');
    }

    public function test_applicant_cannot_reach_staff_areas(): void
    {
        $applicant = $this->createUser('Aspirante');

        $this->actingAs($applicant)->get('/estudiantes')->assertForbidden();
        $this->actingAs($applicant)->get('/user')->assertForbidden();
        $this->actingAs($applicant)->get('/estudiantes/create')->assertForbidden();
    }

    public function test_teacher_home_is_the_welcome_page(): void
    {
        $teacher = $this->createUser('Docente');

        $this->actingAs($teacher)->get('/home')->assertOk()->assertSee('entregas que te asignen');
    }

    public function test_suspended_account_cannot_log_in(): void
    {
        $this->createUser('Asistente', ['email' => 'suspendida@gabook.test', 'status' => 'suspendido']);

        $this->from('/login')->post('/login', ['email' => 'suspendida@gabook.test', 'password' => 'password'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_account_cannot_log_in(): void
    {
        $this->createUser('Asistente', ['email' => 'inactiva@gabook.test', 'status' => 'inactivo']);

        $this->post('/login', ['email' => 'inactiva@gabook.test', 'password' => 'password']);

        $this->assertGuest();
    }

    public function test_session_ends_when_the_account_is_suspended(): void
    {
        $user = $this->createUser('Asistente');
        $this->actingAs($user)->get('/home')->assertOk();

        $user->forceFill(['status' => 'suspendido'])->save();

        $this->actingAs($user->fresh())->get('/home')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_suspended_account_is_rejected_by_the_api(): void
    {
        $admin = $this->createUser('Administrador', ['status' => 'suspendido']);

        $this->actingAs($admin, 'sanctum')->getJson('/api/v1/users')->assertForbidden();
    }

    public function test_admin_suspends_an_account_from_the_edit_form(): void
    {
        $admin = $this->createUser('Administrador');
        $target = $this->createUser('Asistente');

        $this->actingAs($admin)->put("/user/{$target->id}", [
            'name' => $target->name,
            'last_name' => $target->last_name,
            'identification_card' => $target->identification_card,
            'email' => $target->email,
            'role' => 'Asistente',
            'status' => 'suspendido',
        ])->assertRedirect(route('user.index'));

        $this->assertSame('suspendido', $target->fresh()->status->value);
    }
}
