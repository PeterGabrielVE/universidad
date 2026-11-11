@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@php
    $stats = [
        ['label' => 'Estudiantes', 'value' => $count_estudiante, 'icon' => 'fa-user-graduate', 'tone' => 'red'],
        ['label' => 'Usuarios', 'value' => $count_usuarios, 'icon' => 'fa-users', 'tone' => 'blue'],
        ['label' => 'Coordinadores', 'value' => $count_coord, 'icon' => 'fa-user-tie', 'tone' => 'green'],
        ['label' => 'Asistentes', 'value' => $count_asis, 'icon' => 'fa-user-friends', 'tone' => 'amber'],
    ];

    // 'route' => null marca módulos en alcance (FUNCTIONAL_SPECIFICATIONS.md §1) aún sin pantalla.
    // Los módulos con pantalla solo se muestran a quien tiene su permiso.
    $sections = [
        'Usuarios y accesos' => [
            ['title' => 'Usuarios', 'desc' => 'Cuentas, roles y estado', 'icon' => 'fa-users-cog', 'tone' => 'blue', 'route' => 'user.index', 'permission' => 'users.view'],
            ['title' => 'Roles', 'desc' => 'Perfiles de acceso', 'icon' => 'fa-user-shield', 'tone' => 'green', 'route' => 'roles.index', 'permission' => 'roles.manage'],
            ['title' => 'Docentes y jurados', 'desc' => 'Asignación a entregas', 'icon' => 'fa-chalkboard-teacher', 'tone' => 'amber', 'route' => null],
        ],
        'Admisiones e inscripciones' => [
            ['title' => 'Aspirantes', 'desc' => 'Requisitos de ingreso', 'icon' => 'fa-user-plus', 'tone' => 'red', 'route' => null],
            ['title' => 'Inscripción', 'desc' => 'Matriculación por lapso', 'icon' => 'fa-clipboard-list', 'tone' => 'blue', 'route' => null],
        ],
        'Control de estudios' => [
            ['title' => 'Estudiantes', 'desc' => 'Expedientes y datos', 'icon' => 'fa-user-graduate', 'tone' => 'red', 'route' => 'estudiantes.index', 'permission' => 'students.view'],
            ['title' => 'Lapsos y cohortes', 'desc' => 'Periodos académicos', 'icon' => 'fa-calendar-alt', 'tone' => 'green', 'route' => 'lapso.index', 'permission' => 'lapses.manage'],
            ['title' => 'Facultades y programas', 'desc' => 'Programas de doctorado', 'icon' => 'fa-university', 'tone' => 'amber', 'route' => null],
            ['title' => 'Pensum', 'desc' => 'Asignaturas por semestre', 'icon' => 'fa-book-open', 'tone' => 'blue', 'route' => null],
        ],
        'Entregas y calificaciones' => [
            ['title' => 'Entregas', 'desc' => 'Avances, tesis y defensa', 'icon' => 'fa-file-upload', 'tone' => 'red', 'route' => null],
            ['title' => 'Calificaciones', 'desc' => 'Notas y récord académico', 'icon' => 'fa-star', 'tone' => 'amber', 'route' => null],
        ],
        'Finanzas y reportes' => [
            ['title' => 'Pagos y aranceles', 'desc' => 'Matrícula y derechos de grado', 'icon' => 'fa-file-invoice-dollar', 'tone' => 'green', 'route' => null],
            ['title' => 'Reportes', 'desc' => 'Informes y exportes', 'icon' => 'fa-chart-bar', 'tone' => 'blue', 'route' => 'reports', 'permission' => 'reports.view'],
            ['title' => 'Constancias y actas', 'desc' => 'Documentos oficiales', 'icon' => 'fa-file-signature', 'tone' => 'red', 'route' => null],
        ],
    ];

    $user = auth()->user();
    foreach ($sections as $title => $modules) {
        $sections[$title] = array_values(array_filter(
            $modules,
            fn ($module) => $module['route'] === null || $user->can($module['permission'])
        ));
    }
@endphp

@section('content')
    <div id="wrapper">

        <!-- Sidebar -->
        @include('layouts.sidebar')
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                @include('layouts.navbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid dash">

                    <div class="dash-hero">
                        <div>
                            <p class="dash-hero__date">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
                            <h1 class="dash-hero__title">Hola, {{ Auth::user()->name }}</h1>
                            <p class="dash-hero__subtitle">Este es el resumen de tu institución.</p>
                        </div>
                        <i class="fas fa-graduation-cap dash-hero__art" aria-hidden="true"></i>
                    </div>

                    <div class="row dash-stats">
                        @foreach ($stats as $stat)
                            <div class="col-xl-3 col-sm-6 mb-4">
                                <div class="dash-stat">
                                    <span class="dash-icon dash-icon--{{ $stat['tone'] }}">
                                        <i class="fas {{ $stat['icon'] }}"></i>
                                    </span>
                                    <div>
                                        <div class="dash-stat__value">{{ number_format($stat['value']) }}</div>
                                        <div class="dash-stat__label">{{ $stat['label'] }}</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @foreach ($sections as $sectionTitle => $modules)
                        <h2 class="dash-section">{{ $sectionTitle }}</h2>
                        <div class="row">
                            @foreach ($modules as $module)
                                <div class="col-xl-3 col-lg-4 col-sm-6 mb-4">
                                    @if ($module['route'])
                                        <a href="{{ route($module['route']) }}" class="dash-module">
                                    @else
                                        <div class="dash-module is-disabled" title="Módulo en desarrollo">
                                    @endif
                                        <span class="dash-icon dash-icon--{{ $module['tone'] }}">
                                            <i class="fas {{ $module['icon'] }}"></i>
                                        </span>
                                        <div class="dash-module__body">
                                            <div class="dash-module__title">{{ $module['title'] }}</div>
                                            <div class="dash-module__desc">{{ $module['desc'] }}</div>
                                        </div>
                                        @if ($module['route'])
                                            <i class="fas fa-arrow-right dash-module__arrow"></i>
                                        @else
                                            <span class="dash-module__soon">Próximamente</span>
                                        @endif
                                    @if ($module['route'])
                                        </a>
                                    @else
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->
@endsection
