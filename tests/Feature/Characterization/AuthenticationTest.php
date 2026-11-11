<?php

namespace Tests\Feature\Characterization;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsers;
use Tests\TestCase;

/**
 * Inventario #1 y #4: inicio/cierre de sesión y cambio de idioma.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    use CreatesUsers;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRoles();
    }

    public function test_login_page_is_reachable_from_root_and_login(): void
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
    }

    public function test_valid_credentials_log_in_and_redirect_home(): void
    {
        $user = $this->createUser('Administrador', ['email' => 'admin@gabook.test']);

        $this->post('/login', ['email' => 'admin@gabook.test', 'password' => 'password'])
            ->assertRedirect('/home');

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $this->createUser('Administrador', ['email' => 'admin@gabook.test']);

        $this->from('/login')
            ->post('/login', ['email' => 'admin@gabook.test', 'password' => 'incorrecta'])
            ->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_ends_the_session(): void
    {
        $user = $this->createUser('Administrador');

        $this->actingAs($user)->post('/logout')->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_language_switch_stores_supported_locale(): void
    {
        $this->from('/login')->post('/language/switch', ['locale' => 'en'])
            ->assertRedirect('/login')
            ->assertSessionHas('locale', 'en');
    }

    public function test_language_switch_ignores_unsupported_locale(): void
    {
        $this->post('/language/switch', ['locale' => 'fr'])->assertSessionMissing('locale');
    }
}
