<?php

namespace App\Exceptions;

use App\Modules\Shared\Domain\DomainException;
use App\Modules\Shared\Domain\NotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    /**
     * Las excepciones de dominio son errores de negocio esperados: no se registran.
     *
     * @var array<int, class-string>
     */
    protected $dontReport = [
        DomainException::class,
    ];

    /** @var array<int, string> */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        // Recurso inexistente → 404.
        $this->renderable(function (NotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 404);
            }

            abort(404, $e->getMessage());
        });

        // Regla de negocio incumplida → 422 (API) o vuelta al formulario con el error en su campo.
        $this->renderable(function (DomainException $e, Request $request) {
            $field = $e->field() ?? 'general';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'errors' => [$field => [$e->getMessage()]],
                ], 422);
            }

            return back()->withInput($request->except($this->dontFlash))->withErrors([$field => $e->getMessage()]);
        });
    }
}
