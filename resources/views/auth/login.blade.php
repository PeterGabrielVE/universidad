<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" id="token">
    <link rel="icon" href="{{ asset('img/basic/favicon.ico') }}" type="image/x-icon">
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>

<body>
    <div class="login-box">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="logo">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="login-field">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" placeholder=" " required autofocus>
                <label for="email">Correo</label>
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="login-field">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" placeholder=" " required value="{{ old('password') }}">
                <label for="password">Contraseña</label>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <button type="submit">Ingresar</button>
        </form>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}',
                timer: 3000
            });
        </script>
    @endif
    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '{{ session('error') }}',
                timer: 3000
            });
        </script>
    @endif
</body>

<script src={{ asset('js/jquery.min.js') }}></script>
<script src={{ asset('js/bootstrap.bundle.min.js') }}></script>
<script src={{ asset('js/jquery.easing.min.js') }}></script>
<script src={{ asset('js/sb-admin-2.min.js') }}></script>
<script src={{ asset('js/functions.js') }}></script>
<script src={{ asset('js/jquery.dataTables.min.js') }}></script>
<script src={{ asset('js/dataTables.bootstrap4.min.js') }}></script>

<script src={{ asset('js/datatables-demo.js') }}></script>

</html>
@yield('js')
