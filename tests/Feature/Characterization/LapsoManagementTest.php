<?php

namespace Tests\Feature\Characterization;

use App\Lapso;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Inventario #16: CRUD de lapsos tal como funciona hoy (formulario modal en
 * pages/lapsos/index y edición por AJAX con editLapso/updateLapso).
 */
class LapsoManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
        $this->actingAs($this->createUser('Administrador'));
    }

    public function test_index_lists_lapses(): void
    {
        Lapso::factory()->create(['name' => 'Lapso Enero 2026-Junio 2026']);

        $this->get('/lapso')->assertOk()->assertSee('Lapso Enero 2026-Junio 2026');
    }

    public function test_store_generates_the_name_from_the_dates(): void
    {
        $this->post('/lapso', [
            'start_lapse' => '2026-07-13',
            'end_lapse' => '2026-12-11',
            'status' => '1',
        ])->assertOk();

        $this->assertDatabaseHas('lapses', [
            'name' => 'Lapso Julio 2026-Diciembre 2026',
            'start_lapse' => '2026-07-13',
            'end_lapse' => '2026-12-11',
            'status' => '1',
        ]);
    }

    public function test_edit_returns_the_lapse_as_json_for_the_modal(): void
    {
        $lapso = Lapso::factory()->create();

        $this->getJson("/editLapso/{$lapso->id}")
            ->assertOk()
            ->assertJsonFragment(['id' => $lapso->id, 'name' => $lapso->name]);
    }

    public function test_update_changes_the_dates(): void
    {
        $lapso = Lapso::factory()->create();

        $this->from('/lapso')->post("/updateLapso/{$lapso->id}", [
            'start_lapse' => '2026-02-02',
            'end_lapse' => '2026-07-03',
            'status' => '1',
        ])->assertRedirect('/lapso');

        $this->assertDatabaseHas('lapses', ['id' => $lapso->id, 'start_lapse' => '2026-02-02']);
    }

    public function test_destroy_deletes_the_lapse(): void
    {
        $lapso = Lapso::factory()->create();

        $this->from('/lapso')->delete("/lapso/{$lapso->id}")->assertRedirect('/lapso');

        $this->assertModelMissing($lapso);
    }
}
