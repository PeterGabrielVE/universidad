<?php

namespace App\Http\Middleware;

use App\Estudiante;
use App\Models\User;
use App\Modules\IdentityAccess\Domain\Permission;
use App\Modules\IdentityAccess\Domain\RoleName;
use App\Modules\IdentityAccess\Infrastructure\UserDirectory;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Acceso a un expediente de estudiante (parámetro de ruta "estudiante" o "id").
 *
 * - Estudiante: solo su propio expediente.
 * - Personal: lectura con students.view, escritura con students.manage, y en
 *   ambos casos el estudiante debe estar en su alcance de programa (Coordinador).
 *
 * Con el modo "scope" solo comprueba el alcance de programa; el permiso lo
 * exige otra capa (p. ej. permission:grades.record en la ruta).
 */
class AuthorizeStudentRecord
{
    public function __construct(private readonly UserDirectory $directory)
    {
    }

    public function handle(Request $request, Closure $next, string $mode = 'record'): Response
    {
        /** @var User $user */
        $user = $request->user();
        $studentId = $request->route('estudiante') ?? $request->route('id');

        if ($user->hasRole(RoleName::Estudiante->value)) {
            $ownId = $user->studentRecord?->id;
            abort_if($studentId !== null && (int) $studentId !== $ownId, Response::HTTP_FORBIDDEN);

            return $next($request);
        }

        if ($mode === 'record') {
            $needed = ($studentId !== null && $request->isMethodSafe())
                ? Permission::StudentsView
                : Permission::StudentsManage;
            abort_unless($user->can($needed->value), Response::HTTP_FORBIDDEN);
        }

        if ($studentId !== null) {
            $record = Estudiante::with('user.programs')->find($studentId);
            abort_if($record === null, Response::HTTP_NOT_FOUND);

            $programs = $record->user?->programs->pluck('id')->all() ?? [];
            abort_unless($this->directory->programScopeOf($user)->coversAny($programs), Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
