<?php

namespace App\Http\Middleware;

use App\Authorization\Roles;
use App\Estudiante;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * El personal accede a cualquier expediente; un estudiante solo al suyo.
 *
 * El id del expediente llega como parámetro "estudiante" (rutas resource)
 * o "id" (rutas de documentos). Las rutas sin id (create/store) solo exigen
 * que el usuario sea estudiante.
 */
class EnsureStudentOwnsRecord
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user->hasAnyRole(Roles::STAFF)) {
            return $next($request);
        }

        if (! $user->hasRole(Roles::ESTUDIANTE)) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $requested = $request->route('estudiante') ?? $request->route('id');

        if ($requested !== null) {
            $ownId = Estudiante::where('user_id', $user->id)->value('id');

            if ($ownId === null || (int) $requested !== (int) $ownId) {
                abort(Response::HTTP_FORBIDDEN);
            }
        }

        return $next($request);
    }
}
