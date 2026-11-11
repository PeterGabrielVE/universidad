<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Hallazgos C1, C2, C5 y C8 de la auditoría: rutas de depuración públicas
 * y rutas de negocio accesibles sin autenticación.
 */
class RouteProtectionTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_token_generation_endpoint_does_not_exist(): void
    {
        $this->get('/generate-token')->assertNotFound();
    }

    public function test_session_dump_endpoint_does_not_exist(): void
    {
        $this->get('/test-session')->assertNotFound();
    }

    /**
     * @dataProvider formerlyPublicGetRoutes
     */
    public function test_guest_is_redirected_to_login_on_get_routes(string $uri): void
    {
        $this->get($uri)->assertRedirect('/login');
    }

    public static function formerlyPublicGetRoutes(): array
    {
        return [
            'kardex' => ['/estudiantes/1/kardex'],
            'formulario de documentos' => ['/documentEstudiante/1'],
            'presentación' => ['/presentations/1'],
            'reportes' => ['/reports'],
            'pdf de estudiante' => ['/download/student/1'],
            'pdf de sede' => ['/download/sede/1'],
            'descarga de documento' => ['/download/document/archivo.pdf'],
            'lapso en json' => ['/editLapso/1'],
            'usuarios por rol' => ['/usuario/directivo'],
            'calificación' => ['/calificacion/estudiante/1'],
        ];
    }

    /**
     * @dataProvider formerlyPublicPostRoutes
     */
    public function test_guest_is_redirected_to_login_on_post_routes(string $uri): void
    {
        $this->post($uri)->assertRedirect('/login');
    }

    public static function formerlyPublicPostRoutes(): array
    {
        return [
            'guardar calificación' => ['/store/calificacion/1'],
            'actualizar lapso' => ['/updateLapso/1'],
            'subir documentos' => ['/documentStore/1'],
            'subir presentación' => ['/presentationStore/1'],
        ];
    }

    public function test_guest_cannot_delete_a_user(): void
    {
        $victim = $this->createUser('Director');

        $this->get("/usuario/delete/{$victim->id}");
        $this->delete("/usuario/delete/{$victim->id}");

        $this->assertNotNull(User::find($victim->id));
    }

    public function test_admin_can_delete_a_user_with_delete_verb(): void
    {
        $admin = $this->createUser('Administrador');
        $victim = $this->createUser('Asistente');

        $this->actingAs($admin)->delete("/usuario/delete/{$victim->id}")->assertRedirect();

        $this->assertNull(User::find($victim->id));
    }

    public function test_user_deletion_is_not_reachable_with_get(): void
    {
        $admin = $this->createUser('Administrador');
        $victim = $this->createUser('Asistente');

        $this->actingAs($admin)->get("/usuario/delete/{$victim->id}")->assertStatus(405);

        $this->assertNotNull(User::find($victim->id));
    }
}
