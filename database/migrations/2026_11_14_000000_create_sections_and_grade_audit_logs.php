<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fase 3c (FUNCTIONAL_SPECIFICATIONS.md §15–§17):
 * - sections: Lapso → Asignatura → Sección → Docente titular. Cada sección tiene su
 *   acta (abierta/cerrada); el cierre puede ser del docente o forzoso del Administrador.
 * - lapses_students.section_id: la inscripción es en una sección. Las inscripciones
 *   anteriores (sin sección) se conservan tal cual.
 * - lapses.passing_grade_applied: nota mínima usada al cerrar el lapso, para que una
 *   corrección posterior recalcule el resultado con la misma regla.
 * - grade_audit_logs: correcciones de notas con justificación obligatoria (NOT NULL).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lapse_id')->constrained('lapses')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->restrictOnDelete();
            $table->string('code', 20);
            $table->foreignId('teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('abierta');
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('closed_forcibly')->default(false);
            $table->timestamps();
            $table->unique(['lapse_id', 'course_id', 'code']);
        });

        Schema::table('lapses_students', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('lapse_id')->constrained('sections')->restrictOnDelete();
            $table->unique(['section_id', 'student_id']);
        });

        Schema::table('lapses', function (Blueprint $table) {
            $table->decimal('passing_grade_applied', 5, 2)->nullable()->after('status');
        });

        Schema::create('grade_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained('lapses_students')->restrictOnDelete();
            $table->decimal('old_note', 5, 2)->nullable();
            $table->decimal('new_note', 5, 2);
            $table->string('old_result', 20)->nullable();
            $table->string('new_result', 20)->nullable();
            $table->text('justification');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            // Copia del nombre: el registro legal se conserva aunque se elimine la cuenta.
            $table->string('changed_by_name');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_audit_logs');

        Schema::table('lapses', function (Blueprint $table) {
            $table->dropColumn('passing_grade_applied');
        });

        Schema::table('lapses_students', function (Blueprint $table) {
            $table->dropUnique(['section_id', 'student_id']);
            $table->dropConstrainedForeignId('section_id');
        });

        Schema::dropIfExists('sections');
    }
};
