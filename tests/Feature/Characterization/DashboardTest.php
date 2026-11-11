<?php

namespace Tests\Feature\Characterization;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Inventario #5: panel principal y redirección del estudiante a su expediente.
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_staff_sees_the_dashboard_with_modules(): void
    {
        $admin = $this->createUser('Director');

        $this->actingAs($admin)->get('/home')
            ->assertOk()
            ->assertSee('Estudiantes')
            ->assertSee('Próximamente');
    }

    public function test_student_with_record_is_sent_to_own_record(): void
    {
        $student = $this->createStudent();

        $this->actingAs($student->user)->get('/home')
            ->assertRedirect(route('estudiantes.show', $student->id));
    }

    public function test_student_without_record_is_sent_to_complete_it(): void
    {
        $user = $this->createUser('Estudiante');

        $this->actingAs($user)->get('/home')->assertRedirect(route('estudiantes.create'));
    }

    public function test_guest_is_sent_to_login(): void
    {
        $this->get('/home')->assertRedirect('/login');
    }
}
