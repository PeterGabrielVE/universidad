@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

{{-- Inicio para roles sin panel institucional (Docente, Aspirante). --}}
@section('content')
    <div id="wrapper">
        @include('layouts.sidebar')

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                @include('layouts.navbar')

                <div class="container-fluid dash">
                    <div class="dash-hero">
                        <div>
                            <p class="dash-hero__date">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
                            <h1 class="dash-hero__title">Hola, {{ Auth::user()->name }}</h1>
                            <p class="dash-hero__subtitle">
                                @if ($role === \App\Modules\IdentityAccess\Domain\RoleName::Aspirante)
                                    Tu solicitud de admisión está registrada. Control de Estudios revisará tus documentos y tu pago.
                                @elseif ($role === \App\Modules\IdentityAccess\Domain\RoleName::Docente)
                                    Aquí verás las entregas que te asignen como jurado.
                                @else
                                    Tu cuenta no tiene un rol asignado. Contacta a Control de Estudios.
                                @endif
                            </p>
                        </div>
                        <i class="fas fa-graduation-cap dash-hero__art" aria-hidden="true"></i>
                    </div>

                    @if ($programs->isNotEmpty())
                        <h2 class="dash-section">Oferta académica</h2>
                        <div class="row">
                            @foreach ($programs as $program)
                                <div class="col-lg-6 mb-4">
                                    <div class="dash-module">
                                        <span class="dash-icon dash-icon--blue"><i class="fas fa-book-reader"></i></span>
                                        <div class="dash-module__body">
                                            <div class="dash-module__title">{{ $program->name }}</div>
                                            <div class="dash-module__desc">{{ $program->description }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <h2 class="dash-section">Próximamente</h2>
                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="dash-module is-disabled">
                                <span class="dash-icon dash-icon--amber"><i class="fas fa-file-upload"></i></span>
                                <div class="dash-module__body">
                                    <div class="dash-module__title">
                                        {{ $role === \App\Modules\IdentityAccess\Domain\RoleName::Docente ? 'Entregas asignadas' : 'Requisitos de ingreso' }}
                                    </div>
                                    <div class="dash-module__desc">Módulo en desarrollo</div>
                                </div>
                                <span class="dash-module__soon">Próximamente</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
