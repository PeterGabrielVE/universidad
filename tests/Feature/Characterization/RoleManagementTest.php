<?php

namespace Tests\Feature\Characterization;

use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Inventario #11: roles tal como los usa la pantalla Vue (listado y borrado por
 * /api/roles; alta y edición por las rutas web /roles).
 */
class RoleManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->actingAs($this->createUser('Administrador'));
    }

    public function test_index_page_embeds_the_roles_for_vue(): void
    {
        $this->get('/roles')->assertOk()->assertSee('Coordinador');
    }

    public function test_store_creates_a_role(): void
    {
        $this->post('/roles', ['name' => 'Jurado', 'guard_name' => 'web'])
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseHas('roles', ['name' => 'Jurado', 'guard_name' => 'web']);
    }

    public function test_store_rejects_duplicate_names(): void
    {
        $this->post('/roles', ['name' => 'Director', 'guard_name' => 'web'])
            ->assertSessionHasErrors('name');
    }

    public function test_update_renames_a_role_and_answers_json(): void
    {
        $role = Role::create(['name' => 'Jurado', 'guard_name' => 'web']);

        $this->putJson("/roles/{$role->id}", ['name' => 'Jurado externo', 'guard_name' => 'web'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'Jurado externo']);
    }

    public function test_api_delete_removes_a_role(): void
    {
        $role = Role::create(['name' => 'Temporal', 'guard_name' => 'web']);

        $this->deleteJson("/api/roles/{$role->id}")->assertNoContent();

        $this->assertModelMissing($role);
    }
}
