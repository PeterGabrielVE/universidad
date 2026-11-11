<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fase 3b (FUNCTIONAL_SPECIFICATIONS.md §15):
 * - program_user (N:M): un usuario pertenece a uno o varios programas. Sustituye
 *   el comodín users.doctorado_id = 3 ("ambos"), que choca con tener N programas.
 *   Migración de datos: 3 → programas 1 y 2; cualquier otro valor → ese programa.
 *   Después users.doctorado_id queda en NULL para que nadie lea el valor viejo.
 * - program_course (N:M): pensum. Una asignatura transversal existe una sola vez y
 *   pertenece a varios programas. No se asignan asignaturas automáticamente.
 */
return new class extends Migration
{
    private const BOTH = 3;
    private const BOTH_PROGRAMS = [1, 2];

    public function up(): void
    {
        Schema::create('program_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('doctorados')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'program_id']);
        });

        Schema::create('program_course', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('doctorados')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['program_id', 'course_id']);
        });

        $existing = DB::table('doctorados')->pluck('id')->map(fn ($id) => (int) $id)->all();
        $rows = [];
        foreach (DB::table('users')->whereNotNull('doctorado_id')->get(['id', 'doctorado_id']) as $user) {
            $programs = (int) $user->doctorado_id === self::BOTH ? self::BOTH_PROGRAMS : [(int) $user->doctorado_id];
            foreach (array_intersect($programs, $existing) as $programId) {
                $rows[] = ['user_id' => $user->id, 'program_id' => $programId, 'created_at' => now(), 'updated_at' => now()];
            }
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('program_user')->insert($chunk);
        }

        DB::table('users')->update(['doctorado_id' => null]);
    }

    public function down(): void
    {
        foreach (DB::table('program_user')->get()->groupBy('user_id') as $userId => $links) {
            $ids = $links->pluck('program_id')->map(fn ($id) => (int) $id)->sort()->values()->all();
            $legacy = $ids === self::BOTH_PROGRAMS ? self::BOTH : $ids[0];
            DB::table('users')->where('id', $userId)->update(['doctorado_id' => $legacy]);
        }

        Schema::dropIfExists('program_course');
        Schema::dropIfExists('program_user');
    }
};
