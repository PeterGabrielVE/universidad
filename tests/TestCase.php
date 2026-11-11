<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * Barrera de seguridad: RefreshDatabase ejecuta migrate:fresh. Si la configuración
     * apuntara a otra base (p. ej. la de desarrollo por una variable de entorno), se
     * detiene antes de tocarla.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $database = (string) config('database.connections.' . config('database.default') . '.database');

        if (! str_ends_with($database, '_testing')) {
            throw new RuntimeException(
                "Las pruebas se negaron a correr contra la base «{$database}»: debe terminar en «_testing» (ver TESTING.md)."
            );
        }
    }
}
