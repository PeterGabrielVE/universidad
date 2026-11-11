<?php

namespace App\Http\Controllers;

use Alert;
use App\Lapso;
use App\Modules\Academic\Application\UseCase\GradeSheets;
use App\Modules\Academic\Application\UseCase\ManageEnrollments;
use App\Modules\Academic\Application\UseCase\ManageSections;
use App\Modules\Academic\Domain\Exception\LapseNotFound;
use App\Modules\Academic\Domain\Exception\SectionNotFound;
use App\Modules\Academic\Domain\Justification;
use App\Modules\Academic\Infrastructure\SectionDirectory;
use App\Modules\IdentityAccess\Domain\Permission;
use App\Modules\IdentityAccess\Domain\ProgramScope;
use App\Modules\IdentityAccess\Infrastructure\UserDirectory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Secciones de un lapso, inscripción y supervisión del acta (personal).
 * El docente opera su acta en TeacherGradeSheetController.
 */
class SectionController extends Controller
{
    public function __construct(
        private readonly SectionDirectory $directory,
        private readonly UserDirectory $users,
    ) {
    }

    /** Punto de entrada a la inscripción: lapsos del alcance del usuario. */
    public function lapses(Request $request, \App\Modules\Academic\Infrastructure\LapseDirectory $lapses): View
    {
        return view('pages.secciones.lapsos', ['lapsos' => $lapses->paginate($this->scope($request), 15)]);
    }

    public function index(Request $request, int $lapso): View
    {
        $lapse = Lapso::with('program')->find($lapso) ?? throw new LapseNotFound($lapso);
        $this->authorizeProgram($request, $lapse->program_id);
        $canManage = $request->user()->can(Permission::SectionsManage->value);

        return view('pages.secciones.index', [
            'lapse' => $lapse,
            'sections' => $this->directory->forLapse($lapse->id),
            'canManage' => $canManage,
            'courses' => $canManage ? $this->directory->pensumCourses($lapse->program_id) : [],
            'teachers' => $canManage ? $this->directory->teachers() : [],
        ]);
    }

    public function store(Request $request, int $lapso, ManageSections $sections): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => ['required', 'integer'],
            'code' => ['required', 'string', 'max:20'],
            'teacher_id' => ['required', 'integer'],
        ]);
        $section = $sections->create($lapso, (int) $data['course_id'], $data['code'], (int) $data['teacher_id'], $this->scope($request));
        Alert::success('Sección', "Sección «{$section->code()}» abierta.");

        return redirect()->route('secciones.index', $lapso);
    }

    public function update(Request $request, int $section, ManageSections $sections): RedirectResponse
    {
        $data = $request->validate(['teacher_id' => ['required', 'integer']]);
        $updated = $sections->assignTeacher($section, (int) $data['teacher_id'], $this->scope($request));
        Alert::success('Sección', 'Docente titular actualizado.');

        return redirect()->route('secciones.show', $updated->id());
    }

    public function destroy(Request $request, int $section, ManageSections $sections): RedirectResponse
    {
        $lapseId = $this->sectionOrFail($section)->lapse_id;
        $sections->delete($section, $this->scope($request));
        Alert::success('Sección', 'Eliminada.');

        return redirect()->route('secciones.index', $lapseId);
    }

    public function show(Request $request, int $section): View
    {
        $model = $this->sectionOrFail($section);
        $this->authorizeProgram($request, $model->lapse->program_id);
        $user = $request->user();

        return view('pages.secciones.show', [
            'section' => $model,
            'enrollments' => $this->directory->enrollments($section),
            'audit' => $this->directory->audit($section),
            'candidates' => $user->can(Permission::StudentsEnroll->value) ? $this->directory->candidates($model) : [],
            'teachers' => $user->can(Permission::SectionsManage->value) ? $this->directory->teachers() : [],
            'can' => [
                'enroll' => $user->can(Permission::StudentsEnroll->value),
                'manage' => $user->can(Permission::SectionsManage->value),
                'override' => $user->can(Permission::GradesOverride->value),
                'forceClose' => $user->can(Permission::GradeSheetsForceClose->value),
            ],
        ]);
    }

    public function enroll(Request $request, int $section, ManageEnrollments $enrollments): RedirectResponse
    {
        $data = $request->validate([
            'student_ids' => ['required', 'array', 'min:1'],
            'student_ids.*' => ['integer', 'distinct'],
        ]);
        foreach ($data['student_ids'] as $studentId) {
            $enrollments->enroll($section, (int) $studentId, $this->scope($request));
        }
        Alert::success('Inscripción', count($data['student_ids']) . ' estudiante(s) preinscrito(s).');

        return redirect()->route('secciones.show', $section);
    }

    public function confirm(Request $request, int $enrollment, ManageEnrollments $enrollments): RedirectResponse
    {
        $enrollments->confirm($enrollment, $this->scope($request));

        return back()->with('status', 'Inscripción confirmada.');
    }

    public function forceClose(Request $request, int $section, GradeSheets $sheets): RedirectResponse
    {
        $request->validate(['confirm' => ['accepted']]);
        $sheets->forceClose($section, $request->user()->id);
        Alert::success('Acta', 'Cierre administrativo forzoso aplicado.');

        return redirect()->route('secciones.show', $section);
    }

    public function correct(Request $request, int $enrollment, GradeSheets $sheets): RedirectResponse
    {
        $data = $request->validate([
            'note' => ['required', 'numeric', 'between:0,100'],
            'justification' => ['required', 'string', 'max:' . Justification::MAX_LENGTH],
        ]);
        $sheets->correct($enrollment, $data['note'], $data['justification'], $request->user()->id);
        Alert::success('Corrección', 'Nota corregida y registrada en auditoría.');

        return back();
    }

    private function sectionOrFail(int $id): \App\Models\Section
    {
        return $this->directory->section($id) ?? throw new SectionNotFound($id);
    }

    private function authorizeProgram(Request $request, ?int $programId): void
    {
        abort_unless($this->scope($request)->covers($programId), 403);
    }

    private function scope(Request $request): ProgramScope
    {
        return $this->users->programScopeOf($request->user());
    }
}
