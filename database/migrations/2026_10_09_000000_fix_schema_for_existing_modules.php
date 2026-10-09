<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Alinea el esquema con lo que ya usan los controladores y vistas:
 * - students se crea al dar de alta el usuario, antes de que el estudiante complete su perfil.
 * - lapses guarda fechas (LapsoController::setDateInDB), no horas.
 * - lapses_students, doctorados y users.sede_id/country_id se usan pero no existían.
 */
class FixSchemaForExistingModules extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE students
            MODIFY lapse_id BIGINT UNSIGNED NULL,
            MODIFY country_id BIGINT UNSIGNED NULL,
            MODIFY equivalency VARCHAR(255) NULL,
            MODIFY status VARCHAR(255) NULL');

        DB::statement('ALTER TABLE lapses
            MODIFY start_lapse DATE NOT NULL,
            MODIFY end_lapse DATE NOT NULL');

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('sede_id')->nullable()->after('rol_id');
            $table->unsignedBigInteger('country_id')->nullable()->after('sede_id');
        });

        Schema::create('doctorados', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('lapses_students', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('lapse_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->decimal('note', 5, 2)->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('lapse_id')->references('id')->on('lapses')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('lapses_students');
        Schema::dropIfExists('doctorados');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['sede_id', 'country_id']);
        });

        DB::statement('ALTER TABLE lapses
            MODIFY start_lapse TIME NOT NULL,
            MODIFY end_lapse TIME NOT NULL');

        DB::statement('ALTER TABLE students
            MODIFY lapse_id BIGINT UNSIGNED NOT NULL,
            MODIFY country_id BIGINT UNSIGNED NOT NULL,
            MODIFY equivalency VARCHAR(255) NOT NULL,
            MODIFY status VARCHAR(255) NOT NULL');
    }
}
