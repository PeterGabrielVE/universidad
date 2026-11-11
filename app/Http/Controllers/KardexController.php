<?php

namespace App\Http\Controllers;

use App\Estudiante;
use App\Modules\Academic\Infrastructure\SectionDirectory;
use Illuminate\View\View;

/**
 * Kardex / récord académico (#20). El acceso lo controla student.access:
 * el estudiante ve el suyo; el personal, según permiso y programa.
 */
class KardexController extends Controller
{
    public function show(int $estudiante, SectionDirectory $directory): View
    {
        $student = Estudiante::with('user.programs')->findOrFail($estudiante);

        return view('pages.estudiantes.kardex', [
            'student' => $student,
            'lapses' => $directory->kardex($student->id),
        ]);
    }
}
