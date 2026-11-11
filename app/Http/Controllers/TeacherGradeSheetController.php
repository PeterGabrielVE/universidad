<?php

namespace App\Http\Controllers;

use Alert;
use App\Modules\Academic\Application\UseCase\GradeSheets;
use App\Modules\Academic\Domain\Exception\SectionNotFound;
use App\Modules\Academic\Infrastructure\SectionDirectory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Actas del docente titular: registrar notas y cerrar el acta (§15).
 */
class TeacherGradeSheetController extends Controller
{
    public function __construct(private readonly SectionDirectory $directory)
    {
    }

    public function index(Request $request): View
    {
        return view('pages.docente.secciones', ['sections' => $this->directory->forTeacher($request->user()->id)]);
    }

    public function show(Request $request, int $section): View
    {
        $model = $this->directory->section($section) ?? throw new SectionNotFound($section);
        abort_unless((int) $model->teacher_id === (int) $request->user()->id, 403);

        return view('pages.docente.acta', [
            'section' => $model,
            'enrollments' => $this->directory->enrollments($section),
        ]);
    }

    public function record(Request $request, int $section, GradeSheets $sheets): RedirectResponse
    {
        $data = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*' => ['nullable', 'numeric', 'between:0,100'],
        ]);
        $sheets->record($section, $request->user()->id, $data['grades']);
        Alert::success('Acta', 'Notas guardadas.');

        return redirect()->route('docente.acta', $section);
    }

    public function close(Request $request, int $section, GradeSheets $sheets): RedirectResponse
    {
        $request->validate(['confirm' => ['accepted']]);
        $sheets->closeByTeacher($section, $request->user()->id);
        Alert::success('Acta', 'Acta cerrada. Ya no puedes modificar las notas.');

        return redirect()->route('docente.acta', $section);
    }
}
