<?php

namespace App\Http\Controllers;

use App\Doctorado;
use App\Estudiante;
use App\Models\User;
use App\Modules\IdentityAccess\Domain\Permission;
use App\Modules\IdentityAccess\Domain\RoleName;
use App\Modules\IdentityAccess\Infrastructure\UserDirectory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Inicio según el rol: el estudiante va a su expediente, el personal con
     * dashboard.view ve el panel institucional y el resto (Docente, Aspirante)
     * una página de bienvenida.
     */
    public function index(Request $request, UserDirectory $directory): View|RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasRole(RoleName::Estudiante->value)) {
            return $user->studentRecord !== null
                ? redirect()->route('estudiantes.show', $user->studentRecord->id)
                : redirect()->route('estudiantes.create');
        }

        if ($user->can(Permission::DashboardView->value)) {
            return view('home.administrador', [
                'count_estudiante' => Estudiante::count(),
                'count_usuarios' => User::count(),
                'count_coord' => $directory->countByRole(RoleName::Coordinador),
                'count_asis' => $directory->countByRole(RoleName::Asistente),
            ]);
        }

        $role = RoleName::tryFrom((string) $user->roles->first()?->name);

        return view('home.basic', [
            'role' => $role,
            // Oferta académica para el Aspirante (FUNCTIONAL_SPECIFICATIONS.md §5).
            'programs' => $role === RoleName::Aspirante ? Doctorado::orderBy('id')->get() : collect(),
        ]);
    }
}
