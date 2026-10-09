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

    // 'route' => null marca módulos que aún no tienen pantalla propia.
    $sections = [
        'Usuarios y accesos' => [
            ['title' => 'Usuarios', 'desc' => 'Cuentas y perfiles', 'icon' => 'fa-users-cog', 'tone' => 'blue', 'route' => 'user.index'],
            ['title' => 'Roles', 'desc' => 'Perfiles de acceso', 'icon' => 'fa-user-shield', 'tone' => 'green', 'route' => 'roles.index'],
            ['title' => 'Permisos', 'desc' => 'Control de acciones', 'icon' => 'fa-key', 'tone' => 'amber', 'route' => null],
            ['title' => 'Administrativo', 'desc' => 'Personal administrativo', 'icon' => 'fa-briefcase', 'tone' => 'red', 'route' => null],
        ],
        'Comunidad educativa' => [
            ['title' => 'Estudiantes', 'desc' => 'Expedientes y datos', 'icon' => 'fa-user-graduate', 'tone' => 'red', 'route' => 'estudiantes.index'],
            ['title' => 'Docentes', 'desc' => 'Planta docente', 'icon' => 'fa-chalkboard-teacher', 'tone' => 'blue', 'route' => null],
            ['title' => 'PPFF', 'desc' => 'Padres de familia', 'icon' => 'fa-user-friends', 'tone' => 'green', 'route' => null],
            ['title' => 'Inscripción', 'desc' => 'Matrícula de alumnos', 'icon' => 'fa-clipboard-list', 'tone' => 'amber', 'route' => null],
        ],
        'Gestión académica' => [
            ['title' => 'Gestión', 'desc' => 'Periodos académicos', 'icon' => 'fa-calendar-alt', 'tone' => 'red', 'route' => 'lapso.index'],
            ['title' => 'Niveles', 'desc' => 'Niveles educativos', 'icon' => 'fa-signal', 'tone' => 'blue', 'route' => null],
            ['title' => 'Grados', 'desc' => 'Grados por nivel', 'icon' => 'fa-layer-group', 'tone' => 'green', 'route' => null],
            ['title' => 'Paralelos', 'desc' => 'Secciones por grado', 'icon' => 'fa-sitemap', 'tone' => 'amber', 'route' => null],
            ['title' => 'Materias', 'desc' => 'Plan de estudios', 'icon' => 'fa-book-open', 'tone' => 'red', 'route' => null],
            ['title' => 'Asignación', 'desc' => 'Docentes y materias', 'icon' => 'fa-tasks', 'tone' => 'blue', 'route' => null],
            ['title' => 'Turnos', 'desc' => 'Horarios de jornada', 'icon' => 'fa-clock', 'tone' => 'green', 'route' => null],
            ['title' => 'Calificaciones', 'desc' => 'Registro de notas', 'icon' => 'fa-star', 'tone' => 'amber', 'route' => null],
            ['title' => 'Kardex', 'desc' => 'Historial académico', 'icon' => 'fa-id-card', 'tone' => 'red', 'route' => null],
        ],
        'Administración' => [
            ['title' => 'Pagos', 'desc' => 'Pensiones y cobros', 'icon' => 'fa-file-invoice-dollar', 'tone' => 'green', 'route' => null],
            ['title' => 'Reportes', 'desc' => 'Informes y exportes', 'icon' => 'fa-chart-bar', 'tone' => 'blue', 'route' => 'reports'],
            ['title' => 'Instituciones', 'desc' => 'Sedes y unidades', 'icon' => 'fa-university', 'tone' => 'amber', 'route' => null],
            ['title' => 'Configuración', 'desc' => 'Ajustes del sistema', 'icon' => 'fa-cogs', 'tone' => 'red', 'route' => null],
        ],
    ];
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
