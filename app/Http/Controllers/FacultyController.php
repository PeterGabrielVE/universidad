<?php

namespace App\Http\Controllers;

use Alert;
use App\Http\Requests\FacultyRequest;
use App\Modules\Academic\Application\UseCase\ManageFaculties;
use App\Modules\Academic\Infrastructure\ProgramDirectory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FacultyController extends Controller
{
    public function __construct(private readonly ManageFaculties $faculties)
    {
    }

    public function index(ProgramDirectory $directory): View
    {
        return view('pages.facultades.index', ['faculties' => $directory->faculties()]);
    }

    public function store(FacultyRequest $request): RedirectResponse
    {
        $faculty = $this->faculties->create($request->facultyName(), $request->description());
        Alert::success('Facultad', "«{$faculty->name}» creada.");

        return redirect()->route('facultades.index');
    }

    public function update(FacultyRequest $request, int $faculty): RedirectResponse
    {
        $updated = $this->faculties->update($faculty, $request->facultyName(), $request->description());
        Alert::success('Facultad', "«{$updated->name}» actualizada.");

        return redirect()->route('facultades.index');
    }

    public function destroy(int $faculty): RedirectResponse
    {
        $this->faculties->delete($faculty);
        Alert::success('Facultad', 'Eliminada.');

        return redirect()->route('facultades.index');
    }
}
