<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cierra la sesión de una cuenta que pasó a inactiva o suspendida mientras
 * estaba conectada. El inicio de sesión ya rechaza esas cuentas (LoginController).
 */
class EnsureAccountIsActive
{
    public const MESSAGE = 'Tu cuenta está inactiva o suspendida. Contacta a Control de Estudios.';

    public function handle(Request $request, Closure $next): Response
    {
        /** @var User|null $user */
        $user = $request->user();

        if ($user === null || $user->status->canLogIn()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => self::MESSAGE], Response::HTTP_FORBIDDEN);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors(['email' => self::MESSAGE]);
    }
}
