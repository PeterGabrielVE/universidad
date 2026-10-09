<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ asset('img/basic/favicon.ico') }}" type="image/x-icon">
    <title>Iniciar sesión · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body class="auth">
    <main class="auth-shell">

        <section class="auth-brand" aria-hidden="true">
            <div class="auth-brand__inner">
                <img src="{{ asset('img/logo.png') }}" alt="" class="auth-brand__logo">
                <h1 class="auth-brand__name">{{ config('app.name') }}</h1>
                <p class="auth-brand__tagline">Gestión académica en un solo lugar.</p>

                <ul class="auth-brand__features">
                    <li><i class="fas fa-user-graduate"></i> Expedientes y seguimiento de estudiantes</li>
                    <li><i class="fas fa-calendar-alt"></i> Lapsos, inscripciones y calificaciones</li>
                    <li><i class="fas fa-chart-bar"></i> Reportes al instante</li>
                </ul>
            </div>
            <p class="auth-brand__footer">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" class="auth-card__logo">

                <h2 class="auth-card__title">Bienvenido de nuevo</h2>
                <p class="auth-card__subtitle">Ingresa tus credenciales para acceder a tu cuenta.</p>

                @if ($errors->any() || session('error'))
                    <div class="auth-alert" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>{{ $errors->first() ?: session('error') }}</span>
                    </div>
                @endif

                @if (session('status') || session('success'))
                    <div class="auth-alert auth-alert--success" role="status">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('status') ?: session('success') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="auth-form" id="login-form" novalidate>
                    @csrf

                    <div class="auth-field">
                        <label for="email">Correo electrónico</label>
                        <div class="auth-input @error('email') is-invalid @enderror">
                            <i class="fas fa-envelope auth-input__icon" aria-hidden="true"></i>
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                placeholder="nombre@institucion.edu" autocomplete="username" required
                                @if (!old('email')) autofocus @endif>
                        </div>
                    </div>

                    <div class="auth-field">
                        <div class="auth-field__row">
                            <label for="password">Contraseña</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="auth-link auth-link--small">¿Olvidaste tu contraseña?</a>
                            @endif
                        </div>
                        <div class="auth-input @error('password') is-invalid @enderror">
                            <i class="fas fa-lock auth-input__icon" aria-hidden="true"></i>
                            <input id="password" type="password" name="password" placeholder="••••••••"
                                autocomplete="current-password" required @if (old('email')) autofocus @endif>
                            <button type="button" class="auth-input__toggle" id="toggle-password"
                                aria-label="Mostrar contraseña" aria-pressed="false">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <p class="auth-hint" id="caps-warning" hidden>
                            <i class="fas fa-arrow-up"></i> Bloq Mayús está activado
                        </p>
                    </div>

                    <label class="auth-check">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span>Mantener sesión iniciada</span>
                    </label>

                    <button type="submit" class="auth-submit" id="login-submit">
                        <span class="auth-submit__label">Ingresar</span>
                        <span class="auth-submit__spinner" aria-hidden="true"></span>
                    </button>
                </form>

                @if (Route::has('register'))
                    <p class="auth-card__footer">
                        ¿No tienes una cuenta? <a href="{{ route('register') }}" class="auth-link">Crear cuenta</a>
                    </p>
                @endif
            </div>
        </section>

    </main>

    <script>
        (function () {
            var password = document.getElementById('password');
            var toggle = document.getElementById('toggle-password');
            var caps = document.getElementById('caps-warning');
            var form = document.getElementById('login-form');
            var submit = document.getElementById('login-submit');

            toggle.addEventListener('click', function () {
                var show = password.type === 'password';
                password.type = show ? 'text' : 'password';
                toggle.setAttribute('aria-pressed', show);
                toggle.setAttribute('aria-label', show ? 'Ocultar contraseña' : 'Mostrar contraseña');
                toggle.firstElementChild.className = show ? 'fas fa-eye-slash' : 'fas fa-eye';
                password.focus();
            });

            function checkCaps(e) {
                if (e.getModifierState) caps.hidden = !e.getModifierState('CapsLock');
            }
            password.addEventListener('keydown', checkCaps);
            password.addEventListener('keyup', checkCaps);
            password.addEventListener('blur', function () { caps.hidden = true; });

            // Quita el estado de error en cuanto el usuario corrige el campo
            form.querySelectorAll('input').forEach(function (input) {
                input.addEventListener('input', function () {
                    input.closest('.auth-input') && input.closest('.auth-input').classList.remove('is-invalid');
                });
            });

            form.addEventListener('submit', function (e) {
                var invalid = Array.prototype.find.call(form.querySelectorAll('input[required]'), function (input) {
                    var bad = !input.checkValidity();
                    input.closest('.auth-input').classList.toggle('is-invalid', bad);
                    return bad;
                });
                if (invalid) {
                    e.preventDefault();
                    invalid.focus();
                    return;
                }
                submit.disabled = true;
                submit.classList.add('is-loading');
            });
        })();
    </script>
</body>

</html>
