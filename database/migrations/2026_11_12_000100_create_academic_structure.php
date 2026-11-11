<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Fase 3a (Academic), FUNCTIONAL_SPECIFICATIONS.md §3, §10 y §13:
 * - faculties: una facultad agrupa varios programas (1:N).
 * - doctorados es la tabla de programas: faculty_id y min_passing_grade (48 por defecto).
 * - lapses.program_id: cada lapso/cohorte pertenece a un programa.
 * - Estados normalizados. Lapsos: '1' → abierto, '0' → cerrado.
 *   Inscripciones: Inscrito → activo; Aprobado/Reprobado → cerrado + result.
 *
 * Solo añade columnas y traduce valores; down() restaura los valores anteriores.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::table('doctorados', function (Blueprint $table) {
            $table->foreignId('faculty_id')->nullable()->after('id')->constrained('faculties')->restrictOnDelete();
            $table->decimal('min_passing_grade', 5, 2)->default(48)->after('description');
        });

        Schema::table('lapses', function (Blueprint $table) {
            $table->foreignId('program_id')->nullable()->after('id')->constrained('doctorados')->restrictOnDelete();
        });
        DB::table('lapses')->where('status', '1')->update(['status' => 'abierto']);
        DB::table('lapses')->where('status', '0')->update(['status' => 'cerrado']);
        DB::table('lapses')->whereNotIn('status', ['abierto', 'cerrado'])->update(['status' => 'planificacion']);

        Schema::table('lapses_students', function (Blueprint $table) {
            $table->string('result', 20)->nullable()->after('status');
        });
        DB::table('lapses_students')->where('status', 'Aprobado')->update(['status' => 'cerrado', 'result' => 'aprobado']);
        DB::table('lapses_students')->where('status', 'Reprobado')->update(['status' => 'cerrado', 'result' => 'reprobado']);
        DB::table('lapses_students')->where('status', 'Inscrito')->orWhereNull('status')->update(['status' => 'activo']);
    }

    public function down(): void
    {
        DB::table('lapses_students')->where('result', 'aprobado')->update(['status' => 'Aprobado']);
        DB::table('lapses_students')->where('result', 'reprobado')->update(['status' => 'Reprobado']);
        DB::table('lapses_students')->where('status', 'activo')->update(['status' => 'Inscrito']);
        Schema::table('lapses_students', function (Blueprint $table) {
            $table->dropColumn('result');
        });

        DB::table('lapses')->where('status', 'cerrado')->update(['status' => '0']);
        DB::table('lapses')->whereIn('status', ['abierto', 'planificacion'])->update(['status' => '1']);
        Schema::table('lapses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('program_id');
        });

        Schema::table('doctorados', function (Blueprint $table) {
            $table->dropColumn('min_passing_grade');
            $table->dropConstrainedForeignId('faculty_id');
        });

        Schema::dropIfExists('faculties');
    }
};
