<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fase 2 (IdentityAccess):
 * - users.status: activo | inactivo | suspendido (FUNCTIONAL_SPECIFICATIONS.md §10).
 * - App\User se unificó en App\Models\User: las relaciones polimórficas de
 *   Spatie y Sanctum guardan la clase, así que se actualiza el model_type.
 *
 * Solo añade y actualiza: no borra datos. users.rol_id queda obsoleta pero se conserva.
 */
return new class extends Migration
{
    private const OLD_MODEL = 'App\\User';
    private const NEW_MODEL = 'App\\Models\\User';

    private const MORPH_TABLES = [
        ['model_has_roles', 'model_type'],
        ['model_has_permissions', 'model_type'],
        ['personal_access_tokens', 'tokenable_type'],
    ];

    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status', 20)->default('activo')->after('email');
        });

        foreach (self::MORPH_TABLES as [$table, $column]) {
            DB::table($table)->where($column, self::OLD_MODEL)->update([$column => self::NEW_MODEL]);
        }
    }

    public function down(): void
    {
        foreach (self::MORPH_TABLES as [$table, $column]) {
            DB::table($table)->where($column, self::NEW_MODEL)->update([$column => self::OLD_MODEL]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
