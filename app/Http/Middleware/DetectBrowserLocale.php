<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class DetectBrowserLocale
{
    protected $supportedLocales = ['es', 'en'];

    public function handle($request, Closure $next)
    {
        if (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
            Log::info('Idioma de la sesión: ' . App::getLocale());
            return $next($request);
        }

        $browserLocale = $request->server('HTTP_ACCEPT_LANGUAGE');
        Log::info('Encabezado Accept-Language: ' . $browserLocale);

        if ($browserLocale) {
            $preferredLocale = substr($browserLocale, 0, 2);
            Log::info('Idioma preferido del navegador (primeros 2 chars): ' . $preferredLocale);

            if (in_array($preferredLocale, $this->supportedLocales)) {
                App::setLocale($preferredLocale);
                Log::info('Idioma establecido (navegador): ' . App::getLocale());
                // Session::put('locale', $preferredLocale);
            } else {
                App::setLocale(config('app.locale'));
                Log::info('Idioma establecido (predeterminado - no compatible): ' . App::getLocale());
            }
        } else {
            App::setLocale(config('app.locale'));
            Log::info('Idioma establecido (predeterminado - sin encabezado): ' . App::getLocale());
        }

        return $next($request);
    }
}
