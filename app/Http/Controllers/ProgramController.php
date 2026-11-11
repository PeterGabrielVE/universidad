<?php

namespace App\Http\Controllers;

use Alert;
use App\Http\Requests\ProgramRequest;
use App\Modules\Academic\Application\UseCase\ManagePensum;
use App\Modules\Academic\Application\UseCase\ManagePrograms;
use App\Modules\Academic\Domain\Exception\ProgramNotFound;
use App\Modules\Academic\Infrastructure\ProgramDirectory;
use App\Modules\IdentityAccess\Domain\Permission;
use App\Modules\IdentityAccess\Domain\ProgramScope;
use App\Modules\IdentityAccess\Infrastructure\UserDirectory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Programas y pensum. Gestionar requiere programs.manage (Administrador);
 * consultar, pensum.view (el Coordinador solo ve sus programas).
 */
class ProgramController extends Controller
{
    public function __construct(
        private readonly ManagePrograms $programs,
        private readonly ProgramDirectory $directory,
        private readonly UserDirectory $users,
    ) {
    }

    public function index(Request $request): View
    {
        return view('pages.programas.index', [
            'programs' => $this->directory->programs($this->scope($request)),
            'faculties' => $this->directory->facultyOptions(),
            'canManage' => $request->user()->can(Permission::ProgramsManage->value),
        ]);
    }

    public function store(ProgramRequest $request): RedirectResponse
    {
        $program = $this->programs->create($request->toInput());
        Alert::success('Programa', "«{$program->name}» creado.");

        return redirect()->route('programas.index');
    }

    public function edit(int $program): View
    {
        return view('pages.programas.edit', [
            'program' => $this->directory->program($program) ?? throw new ProgramNotFound($program),
            'faculties' => $this->directory->facultyOptions(),
        ]);
    }

    public function update(ProgramRequest $request, int $program): RedirectResponse
    {
        $updated = $this->programs->update($program, $request->toInput());
        Alert::success('Programa', "«{$updated->name}» actualizado.");

        return redirect()->route('programas.index');
    }

    public function destroy(int $program): RedirectResponse
    {
        $this->programs->delete($program);
        Alert::success('Programa', 'Eliminado.');

        return redirect()->route('programas.index');
    }

    public function pensum(Request $request, int $program): View
    {
        abort_unless($this->scope($request)->covers($program), 403);
        $model = $this->directory->program($program) ?? throw new ProgramNotFound($program);
        $canManage = $request->user()->can(Permission::ProgramsManage->value);

        return view('pages.programas.pensum', [
            'program' => $model,
            'semesters' => $this->directory->pensumBySemester($program),
            'available' => $canManage ? $this->directory->coursesOutside($program) : [],
            'canManage' => $canManage,
        ]);
    }

    public function attachCourse(Request $request, int $program, ManagePensum $pensum): RedirectResponse
    {
        $courseIds = $request->validate([
            'course_ids' => ['required', 'array', 'min:1'],
            'course_ids.*' => ['integer', 'distinct'],
        ])['course_ids'];

        foreach ($courseIds as $courseId) {
            $pensum->attach($program, (int) $courseId);
        }
        Alert::success('Pensum', count($courseIds) . ' asignatura(s) añadida(s).');

        return redirect()->route('programas.pensum', $program);
    }

    public function detachCourse(int $program, int $course, ManagePensum $pensum): RedirectResponse
    {
        $pensum->detach($program, $course);
        Alert::success('Pensum', 'Asignatura retirada del pensum.');

        return redirect()->route('programas.pensum', $program);
    }

    private function scope(Request $request): ProgramScope
    {
        return $this->users->programScopeOf($request->user());
    }
}
