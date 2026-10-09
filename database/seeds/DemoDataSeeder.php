<?php

use App\Estudiante;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Datos de prueba para desarrollo. Se puede ejecutar varias veces:
 * borra y recrea los usuarios @gabook.test y sus inscripciones.
 *
 *   php artisan db:seed --class=DemoDataSeeder
 *
 * Todos los usuarios de prueba tienen la contraseña "password".
 */
class DemoDataSeeder extends Seeder
{
    private const DOMAIN = '@gabook.test';

    public function run(): void
    {
        $this->call(RolesTableSeeder::class);

        mt_srand(2026);

        DB::transaction(function () {
            User::where('email', 'like', '%' . self::DOMAIN)->get()->each->delete();

            $sedes = $this->seedSedes();
            $doctorados = $this->seedDoctorados();
            $lapsos = $this->seedLapsos();

            $this->seedStaff($sedes);
            $this->seedStudents($sedes, $doctorados, $lapsos);
        });
    }

    private function seedSedes(): array
    {
        $ids = [];
        foreach (['Sede Central', 'Sede Norte', 'Sede Sur'] as $name) {
            DB::table('headquarters')->updateOrInsert(['name' => $name], ['created_at' => now(), 'updated_at' => now()]);
            $ids[] = DB::table('headquarters')->where('name', $name)->value('id');
        }
        return $ids;
    }

    private function seedDoctorados(): array
    {
        // EstudiantesController::store trata el id 1 como Gerencia y el resto como Ciencias.
        $doctorados = [
            1 => ['Doctorado en Gerencia Avanzada', 'Programa doctoral orientado a la gestión de organizaciones.'],
            2 => ['Doctorado en Ciencias de la Educación', 'Programa doctoral en investigación educativa.'],
        ];
        foreach ($doctorados as $id => [$name, $description]) {
            DB::table('doctorados')->updateOrInsert(
                ['id' => $id],
                ['name' => $name, 'description' => $description, 'created_at' => now(), 'updated_at' => now()]
            );
        }
        return array_keys($doctorados);
    }

    /**
     * Nombres con el mismo formato que genera LapsoController::store.
     * status '0' = cerrado, '1' = activo.
     */
    private function seedLapsos(): array
    {
        $lapsos = [
            ['Lapso Enero 2025-Junio 2025', '2025-01-13', '2025-06-27', '0'],
            ['Lapso Julio 2025-Diciembre 2025', '2025-07-14', '2025-12-12', '0'],
            ['Lapso Enero 2026-Junio 2026', '2026-01-12', '2026-06-26', '0'],
            ['Lapso Julio 2026-Diciembre 2026', '2026-07-13', '2026-12-11', '1'],
        ];

        $ids = [];
        foreach ($lapsos as [$name, $start, $end, $status]) {
            DB::table('lapses')->updateOrInsert(
                ['name' => $name],
                ['start_lapse' => $start, 'end_lapse' => $end, 'status' => $status, 'created_at' => now(), 'updated_at' => now()]
            );
            $ids[] = DB::table('lapses')->where('name', $name)->value('id');
        }
        return $ids;
    }

    private function seedStaff(array $sedes): void
    {
        $staff = [
            // [nombre, apellido, cédula, rol_id, rol]
            ['Carmen', 'Rodríguez', 9876543, 1, 'Director'],
            ['Luis', 'Fernández', 12345678, 2, 'Coordinador'],
            ['Patricia', 'Gómez', 13456789, 2, 'Coordinador'],
            ['Andrés', 'Morales', 15678901, 3, 'Asistente'],
            ['Valentina', 'Rojas', 16789012, 3, 'Asistente'],
            ['Jorge', 'Castillo', 14567890, 3, 'Asistente'],
            ['Sofía', 'Herrera', 11234567, 5, 'Administrador'],
        ];

        foreach ($staff as $i => [$name, $lastName, $ci, $rolId, $rol]) {
            $user = $this->makeUser($name, $lastName, $ci, $rolId, [
                'sede_id' => $sedes[$i % count($sedes)],
            ]);
            $user->assignRole($rol);
        }
    }

    private function seedStudents(array $sedes, array $doctorados, array $lapsos): void
    {
        $paises = DB::table('countries')->whereIn('code', ['VE', 'CO', 'BO', 'PE'])->pluck('id', 'code');
        $activo = end($lapsos);
        $cerrados = array_slice($lapsos, 0, -1);

        $students = [
            ['María', 'González', 'VE'], ['José', 'Pérez', 'VE'], ['Ana', 'Martínez', 'VE'],
            ['Carlos', 'López', 'CO'], ['Daniela', 'Sánchez', 'VE'], ['Miguel', 'Ramírez', 'BO'],
            ['Gabriela', 'Torres', 'VE'], ['Ricardo', 'Flores', 'PE'], ['Isabel', 'Rivera', 'VE'],
            ['Fernando', 'Díaz', 'CO'], ['Lucía', 'Vargas', 'VE'], ['Héctor', 'Mendoza', 'VE'],
        ];

        foreach ($students as $i => [$name, $lastName, $pais]) {
            $user = $this->makeUser($name, $lastName, 20000000 + $i * 731, 4, [
                'sede_id' => $sedes[$i % count($sedes)],
                'country_id' => $paises[$pais] ?? null,
                'doctorado_id' => $doctorados[$i % count($doctorados)],
            ]);
            $user->assignRole('Estudiante');

            $student = Estudiante::create([
                'user_id' => $user->id,
                'lapse_id' => $activo,
                'country_id' => $paises[$pais] ?? null,
                'equivalency' => $i % 5 === 0 ? 'Sí' : 'No',
                'status' => '1',
            ]);

            // Cada estudiante empezó en un lapso distinto: cursa un semestre por lapso.
            $historial = array_slice($cerrados, $i % count($cerrados));
            $semestre = 1;
            foreach ($historial as $lapso) {
                $this->enroll($student->id, $lapso, $semestre++, true);
            }
            $this->enroll($student->id, $activo, $semestre, false);
        }
    }

    private function enroll(int $studentId, int $lapseId, int $semester, bool $closed): void
    {
        $courses = DB::table('courses')->where('semester_id', $semester)->pluck('id');

        $rows = $courses->map(function ($courseId) use ($studentId, $lapseId, $closed) {
            $note = $closed ? mt_rand(35, 100) : null;
            return [
                'student_id' => $studentId,
                'lapse_id' => $lapseId,
                'course_id' => $courseId,
                'note' => $note,
                // Misma regla que LapsoController::update al cerrar un lapso.
                'status' => $closed ? ($note > 49 ? 'Aprobado' : 'Reprobado') : 'Inscrito',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        DB::table('lapses_students')->insert($rows->all());
    }

    private function makeUser(string $name, string $lastName, int $ci, int $rolId, array $extra = []): User
    {
        $email = $this->slug($name) . '.' . $this->slug($lastName) . self::DOMAIN;

        return User::create(array_merge([
            'name' => $name,
            'last_name' => $lastName,
            'identification_card' => $ci,
            'phone' => '0414' . str_pad((string) mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT),
            'email' => $email,
            'password' => bcrypt('password'),
            'rol_id' => $rolId,
        ], $extra));
    }

    private function slug(string $value): string
    {
        return strtolower(Str::ascii($value));
    }
}
