<?php

use App\Estudiante;
use App\Models\User;
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

    /** Docente titular de las secciones demo. */
    private ?int $teacherId = null;

    public function run(): void
    {
        $this->call(RolesTableSeeder::class);

        mt_srand(2026);

        DB::transaction(function () {
            $demoStudents = DB::table('students')->join('users', 'users.id', '=', 'students.user_id')
                ->where('users.email', 'like', '%' . self::DOMAIN)->pluck('students.id');
            DB::table('grade_audit_logs')->whereIn('enrollment_id', DB::table('lapses_students')->whereIn('student_id', $demoStudents)->select('id'))->delete();
            User::where('email', 'like', '%' . self::DOMAIN)->get()->each->delete();

            $sedes = $this->seedSedes();
            $doctorados = $this->seedDoctorados();
            $lapsos = $this->seedLapsos($doctorados);

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

    /**
     * Facultades y programas. La nota mínima varía por programa (§3): Gerencia
     * usa 75 para mostrar la diferencia; Ciencias de la Educación, la base de 48.
     */
    private function seedDoctorados(): array
    {
        $faculties = [
            'Facultad de Ciencias Económicas y Sociales' => 'Programas de gestión y economía.',
            'Facultad de Humanidades y Educación' => 'Programas de educación e investigación educativa.',
        ];
        foreach ($faculties as $name => $description) {
            DB::table('faculties')->updateOrInsert(['name' => $name], ['description' => $description, 'created_at' => now(), 'updated_at' => now()]);
        }
        $facultyId = fn (string $name) => DB::table('faculties')->where('name', $name)->value('id');

        // EstudiantesController::store trata el id 1 como Gerencia y el resto como Ciencias.
        $doctorados = [
            1 => ['Doctorado en Gerencia Avanzada', 'Programa doctoral orientado a la gestión de organizaciones.', 'Facultad de Ciencias Económicas y Sociales', 75],
            2 => ['Doctorado en Ciencias de la Educación', 'Programa doctoral en investigación educativa.', 'Facultad de Humanidades y Educación', 48],
        ];
        foreach ($doctorados as $id => [$name, $description, $faculty, $minGrade]) {
            DB::table('doctorados')->updateOrInsert(
                ['id' => $id],
                [
                    'name' => $name,
                    'description' => $description,
                    'faculty_id' => $facultyId($faculty),
                    'min_passing_grade' => $minGrade,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
        // Pensum demo: ambos programas comparten las asignaturas sembradas (N:M).
        $courseIds = DB::table('courses')->pluck('id');
        foreach (array_keys($doctorados) as $programId) {
            DB::table('program_course')->insertOrIgnore($courseIds->map(fn ($courseId) => [
                'program_id' => $programId,
                'course_id' => $courseId,
                'created_at' => now(),
                'updated_at' => now(),
            ])->all());
        }

        return array_keys($doctorados);
    }

    /**
     * Cada programa tiene sus propios lapsos: tres cerrados y uno abierto.
     * Nombres con el formato de LapsePeriod::name().
     *
     * @return array<int, list<int>> id de programa => ids de lapso en orden cronológico
     */
    private function seedLapsos(array $programIds): array
    {
        $lapsos = [
            ['Lapso Enero 2025-Junio 2025', '2025-01-13', '2025-06-27', 'cerrado'],
            ['Lapso Julio 2025-Diciembre 2025', '2025-07-14', '2025-12-12', 'cerrado'],
            ['Lapso Enero 2026-Junio 2026', '2026-01-12', '2026-06-26', 'cerrado'],
            ['Lapso Julio 2026-Diciembre 2026', '2026-07-13', '2026-12-11', 'abierto'],
        ];

        $ids = [];
        foreach ($programIds as $programId) {
            foreach ($lapsos as [$name, $start, $end, $status]) {
                DB::table('lapses')->updateOrInsert(
                    ['name' => $name, 'program_id' => $programId],
                    ['start_lapse' => $start, 'end_lapse' => $end, 'status' => $status, 'created_at' => now(), 'updated_at' => now()]
                );
                $ids[$programId][] = DB::table('lapses')->where('name', $name)->where('program_id', $programId)->value('id');
            }
        }
        foreach (DB::table('doctorados')->pluck('min_passing_grade', 'id') as $programId => $minGrade) {
            DB::table('lapses')->where('program_id', $programId)->where('status', 'cerrado')
                ->update(['passing_grade_applied' => $minGrade]);
        }

        return $ids;
    }

    private function seedStaff(array $sedes): void
    {
        $staff = [
            // [nombre, apellido, cédula, rol, programa (1 Gerencia, 2 Ciencias)]
            ['Carmen', 'Rodríguez', 9876543, 'Director', null],
            ['Luis', 'Fernández', 12345678, 'Coordinador', 2],
            ['Patricia', 'Gómez', 13456789, 'Coordinador', 1],
            ['Andrés', 'Morales', 15678901, 'Asistente', null],
            ['Valentina', 'Rojas', 16789012, 'Asistente', null],
            ['Jorge', 'Castillo', 14567890, 'Asistente', null],
            ['Roberto', 'Salas', 10987654, 'Docente', null],
            ['Sofía', 'Herrera', 11234567, 'Administrador', null],
        ];

        foreach ($staff as $i => [$name, $lastName, $ci, $rol, $programa]) {
            $user = $this->makeUser($name, $lastName, $ci, [
                'sede_id' => $sedes[$i % count($sedes)],
            ]);
            $user->assignRole($rol);
            $user->programs()->sync($programa === null ? [] : [$programa]);

            if ($rol === 'Docente') {
                $this->teacherId = $user->id;
            }
        }
    }

    private function seedStudents(array $sedes, array $doctorados, array $lapsos): void
    {
        $paises = DB::table('countries')->whereIn('code', ['VE', 'CO', 'BO', 'PE'])->pluck('id', 'code');
        $minGrades = DB::table('doctorados')->pluck('min_passing_grade', 'id');

        $students = [
            ['María', 'González', 'VE'], ['José', 'Pérez', 'VE'], ['Ana', 'Martínez', 'VE'],
            ['Carlos', 'López', 'CO'], ['Daniela', 'Sánchez', 'VE'], ['Miguel', 'Ramírez', 'BO'],
            ['Gabriela', 'Torres', 'VE'], ['Ricardo', 'Flores', 'PE'], ['Isabel', 'Rivera', 'VE'],
            ['Fernando', 'Díaz', 'CO'], ['Lucía', 'Vargas', 'VE'], ['Héctor', 'Mendoza', 'VE'],
        ];

        foreach ($students as $i => [$name, $lastName, $pais]) {
            $programa = $doctorados[$i % count($doctorados)];
            $activo = end($lapsos[$programa]);
            $cerrados = array_slice($lapsos[$programa], 0, -1);
            $minGrade = (float) $minGrades[$programa];

            $user = $this->makeUser($name, $lastName, 20000000 + $i * 731, [
                'sede_id' => $sedes[$i % count($sedes)],
                'country_id' => $paises[$pais] ?? null,
            ]);
            $user->assignRole('Estudiante');
            $user->programs()->sync([$programa]);

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
                $this->enroll($student->id, $lapso, $semestre++, $minGrade);
            }
            $this->enroll($student->id, $activo, $semestre, null);
        }
    }

    /**
     * Inscribe en la sección "A" de cada asignatura del semestre (la crea con el docente demo).
     * Con $minGrade (lapso cerrado): acta cerrada por el docente, nota y resultado fijado.
     * Sin $minGrade (lapso abierto): acta abierta e inscripción activa, aún sin nota.
     */
    private function enroll(int $studentId, int $lapseId, int $semester, ?float $minGrade): void
    {
        $courses = DB::table('courses')->where('semester_id', $semester)->pluck('id');
        $closed = $minGrade !== null;

        $rows = $courses->map(function ($courseId) use ($studentId, $lapseId, $minGrade, $closed) {
            DB::table('sections')->updateOrInsert(
                ['lapse_id' => $lapseId, 'course_id' => $courseId, 'code' => 'A'],
                [
                    'teacher_id' => $this->teacherId,
                    'status' => $closed ? 'cerrada' : 'abierta',
                    'closed_at' => $closed ? now() : null,
                    'closed_by' => $closed ? $this->teacherId : null,
                    'closed_forcibly' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
            $sectionId = DB::table('sections')->where(['lapse_id' => $lapseId, 'course_id' => $courseId, 'code' => 'A'])->value('id');

            $note = $closed ? mt_rand(35, 100) : null;
            return [
                'student_id' => $studentId,
                'lapse_id' => $lapseId,
                'section_id' => $sectionId,
                'course_id' => $courseId,
                'note' => $note,
                'status' => $minGrade !== null ? 'cerrado' : 'activo',
                'result' => $minGrade !== null ? ($note >= $minGrade ? 'aprobado' : 'reprobado') : null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        });

        DB::table('lapses_students')->insert($rows->all());
    }

    private function makeUser(string $name, string $lastName, int $ci, array $extra = []): User
    {
        $email = $this->slug($name) . '.' . $this->slug($lastName) . self::DOMAIN;

        return User::create(array_merge([
            'name' => $name,
            'last_name' => $lastName,
            'identification_card' => $ci,
            'phone' => '0414' . str_pad((string) mt_rand(0, 9999999), 7, '0', STR_PAD_LEFT),
            'email' => $email,
            'password' => bcrypt('password'),
            'status' => 'activo',
        ], $extra));
    }

    private function slug(string $value): string
    {
        return strtolower(Str::ascii($value));
    }
}
