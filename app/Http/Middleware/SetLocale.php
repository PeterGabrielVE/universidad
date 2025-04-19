<?php

namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        } else {
            $defaultLocale = config('app.locale');
            session(['locale' => $defaultLocale]); // Guardar el predeterminado en la sesión
            App::setLocale($defaultLocale);
        }

        return $next($request);
    }
}
