<?php

namespace App\Modules\Academic\Infrastructure;

use App\Asignatura;
use App\Estudiante;
use App\Lapso;
use App\Lapso_Estudiante;
use App\Models\Section;
use App\Models\User;
use App\Modules\Academic\Domain\EnrollmentStatus;
use App\Modules\IdentityAccess\Domain\AccountStatus;
use App\Modules\IdentityAccess\Domain\RoleName;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Lado de lectura de secciones, actas, inscripciones, auditoría y kardex.
 */
final class SectionDirectory
{
    /** @return Collection<int, Section> */
    public function forLapse(int $lapseId): Collection
    {
        return $this->withCounts(Section::query()->where('lapse_id', $lapseId))
            ->with(['course', 'teacher', 'closer'])
            ->get()
            ->sortBy(fn (Section $s) => [$s->course?->semester_id, $s->course?->cod, $s->code])
            ->values();
    }

    /** @return Collection<int, Section> */
    public function forTeacher(int $teacherId): Collection
    {
        return $this->withCounts(Section::query()->where('teacher_id', $teacherId))
            ->with(['course', 'lapse.program'])
            ->latest('id')
            ->get();
    }

    public function section(int $id): ?Section
    {
        return Section::with(['lapse.program', 'course', 'teacher', 'closer'])->find($id);
    }

    /** @return Collection<int, Lapso_Estudiante> */
    public function enrollments(int $sectionId): Collection
    {
        return Lapso_Estudiante::with('estudiante.user')
            ->where('section_id', $sectionId)
            ->get()
            ->sortBy(fn (Lapso_Estudiante $e) => mb_strtolower(($e->estudiante?->user?->last_name ?? '') . ' ' . ($e->estudiante?->user?->name ?? '')))
            ->values();
    }

    /** @return array<int, string> estudiantes del programa aún no inscritos en esa asignatura del lapso */
    public function candidates(Section $section): array
    {
        $lapse = $section->lapse;

        return Estudiante::query()
            ->with('user')
            ->whereHas('user.programs', fn ($q) => $q->whereKey($lapse->program_id))
            ->whereDoesntHave('enrollments', fn ($q) => $q->where('lapse_id', $lapse->id)->where('course_id', $section->course_id))
            ->get()
            ->sortBy(fn (Estudiante $e) => mb_strtolower($e->user?->last_name . ' ' . $e->user?->name))
            ->mapWithKeys(fn (Estudiante $e) => [$e->id => "{$e->user?->last_name}, {$e->user?->name} ({$e->user?->identification_card})"])
            ->all();
    }

    /** @return array<int, string> docentes activos */
    public function teachers(): array
    {
        return User::role(RoleName::Docente->value)
            ->where('status', AccountStatus::Activo->value)
            ->orderBy('last_name')
            ->get()
            ->mapWithKeys(fn (User $u) => [$u->id => trim("{$u->last_name}, {$u->name}")])
            ->all();
    }

    /** @return array<int, string> asignaturas del pensum del programa */
    public function pensumCourses(?int $programId): array
    {
        if ($programId === null) {
            return [];
        }

        return Asignatura::query()
            ->whereHas('programs', fn ($q) => $q->whereKey($programId))
            ->orderBy('semester_id')->orderBy('cod')
            ->get()
            ->mapWithKeys(fn (Asignatura $c) => [$c->id => "{$c->cod} — {$c->name} (semestre {$c->semester_id})"])
            ->all();
    }

    /** @return Collection<int, object> correcciones de notas de la sección, más recientes primero */
    public function audit(int $sectionId): Collection
    {
        return DB::table('grade_audit_logs')
            ->join('lapses_students', 'lapses_students.id', '=', 'grade_audit_logs.enrollment_id')
            ->join('students', 'students.id', '=', 'lapses_students.student_id')
            ->join('users', 'users.id', '=', 'students.user_id')
            ->where('lapses_students.section_id', $sectionId)
            ->orderByDesc('grade_audit_logs.id')
            ->get([
                'grade_audit_logs.*',
                DB::raw("CONCAT(users.last_name, ', ', users.name) AS student_name"),
            ]);
    }

    /**
     * Kardex: todas las inscripciones del estudiante agrupadas por lapso (más reciente primero).
     *
     * @return Collection<int, array{lapse: Lapso, enrollments: Collection<int, Lapso_Estudiante>}>
     */
    public function kardex(int $studentId): Collection
    {
        return Lapso_Estudiante::with(['lapso.program', 'asignatura', 'section'])
            ->where('student_id', $studentId)
            ->get()
            ->groupBy('lapse_id')
            ->map(fn (Collection $rows) => ['lapse' => $rows->first()->lapso, 'enrollments' => $rows->sortBy(fn ($r) => $r->asignatura?->cod)->values()])
            ->sortByDesc(fn (array $group) => $group['lapse']?->start_lapse)
            ->values();
    }

    private function withCounts($query)
    {
        return $query->withCount([
            'enrollments',
            'enrollments as active_count' => fn ($q) => $q->where('status', EnrollmentStatus::Activo->value),
            'enrollments as pending_count' => fn ($q) => $q->where('status', EnrollmentStatus::PreInscrito->value),
            'enrollments as ungraded_count' => fn ($q) => $q->where('status', EnrollmentStatus::Activo->value)->whereNull('note'),
        ]);
    }
}
