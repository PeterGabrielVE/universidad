{{-- Cada enlace se muestra según el permiso que exige su ruta (AccessPolicy). --}}
@php($authUser = Auth::user())
@php($ownRecordId = $authUser->studentRecord?->id)
<ul class="navbar-nav bg-white sidebar sidebar-uft accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex flex-column align-items-center justify-content-center py-3 mt-3" href="{{ route('home') }}">
                <img src="{{URL::asset('img/logo.png')}}" alt="Gabook Logo" style="height: 60px; width: auto; display: block;">
                <div class="sidebar-brand-text text-uppercase fw-bold text-white mt-2" style="font-size: 1.2rem;">GABOOK</div>
            </a>

            <hr class="sidebar-divider my-0">

            <li class="nav-item active">
                <a class="nav-link text-uft" href="{{ route('home') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Principal</span></a>
            </li>

            <hr class="sidebar-divider">

            @canany(['users.view', 'students.view'])
            <li class="nav-item">
                <a class="nav-link collapsed text-uft" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Usuarios</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-uft py-2 collapse-inner rounded">
                        @can('users.view')
                        <a class="collapse-item text-white btn-uft-li" href="{{ route('user.index') }}">Todos</a>
                        <a class="collapse-item text-white btn-uft-li" href="{{ route('usuario/directivo') }}">Coordinadores</a>
                        <a class="collapse-item text-white btn-uft-li" href="{{ route('usuario/asistente') }}">Asistentes de Postgrado</a>
                        @endcan
                        @can('students.view')
                        <a class="collapse-item text-white btn-uft-li" href="{{ route('estudiantes.index') }}">Estudiantes</a>
                        @endcan
                    </div>
                </div>
            </li>
            <hr class="sidebar-divider">
            @endcanany

            @can('roles.manage')
            <li class="nav-item">
                <a class="nav-link text-uft" href="{{ route('roles.index') }}">
                    <i class="fas fa-fw fa-user-shield"></i>
                    <span>Roles</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            @endcan

            @canany(['programs.manage', 'pensum.view'])
            <li class="nav-item">
                <a class="nav-link text-uft" href="{{ route('programas.index') }}">
                    <i class="fas fa-fw fa-university"></i>
                    <span>Programas y pensum</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            @endcanany

            @can('reports.view')
            <li class="nav-item">
                <a class="nav-link collapsed text-uft" href="{{ route('reports') }}">
                    <i class="fas fa-fw fa-chart-bar"></i>
                    <span>Reportes</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            @endcan

            @canany(['lapses.manage', 'lapses.manage_program'])
            <li class="nav-item">
                <a class="nav-link collapsed text-uft" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Configuración</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-uft  py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('lapso.index') }}">Lapso</a>
                    </div>
                </div>
            </li>
            <hr class="sidebar-divider">
            @endcanany

            @if ($ownRecordId !== null)
            <li class="nav-item">
                <a class="nav-link collapsed text-uft" href="{{ route('estudiantes.show', $ownRecordId) }}">
                    <span>Mi perfil</span>
                </a>
            </li>
            <hr class="sidebar-divider">

                {{-- Las pantallas de entregas existen para Gerencia (1) y Ciencias (2); se rehacen en la Fase 4. --}}
                @php($inGerencia = $authUser->belongsToProgram(1))
                @php($inCiencias = $authUser->belongsToProgram(2))
                @if ($inGerencia && $inCiencias)
                    <li class="nav-item">
                        <a class="nav-link collapsed text-uft" href="#" data-toggle="collapse" data-target="#collapseFour"
                        aria-expanded="true" aria-controls="collapseFour">
                        <i class="fas fa-fw fa-users"></i>
                        <span>Mis Documentos</span>
                    </a>
                    <div id="collapseFour" class="collapse" aria-labelledby="collapseFour" data-parent="#accordionSidebar">
                        <div class="bg-uft py-2 collapse-inner rounded">
                            <a class="collapse-item" href="{{ route('estudiantes/documentos/ciencias', $ownRecordId) }}">Ciencias de la Educación</a>
                            <a class="collapse-item" href="{{ route('estudiantes/documentos/gerencia', $ownRecordId) }}">Gerencia Avanzada</a>
                        </div>
                    </div>
                    </li>
                @else
                <li class="nav-item">
                    @if ($inGerencia)
                    <a class="nav-link collapsed text-uft" href="{{ route('estudiantes/documentos/gerencia', $ownRecordId) }}">
                        <span>Mis Documentos</span>
                    </a>
                    @endif
                    @if ($inCiencias)
                    <a class="nav-link collapsed text-uft" href="{{ route('estudiantes/documentos/ciencias', $ownRecordId) }}">
                        <span>Mis Documentos</span>
                    </a>
                    @endif
                </li>
                @endif
            <hr class="sidebar-divider">
            @endif
        </ul>
