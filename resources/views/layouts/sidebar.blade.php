<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="{{ route('home') }}">
                <div class="sidebar-brand-icon rotate-n-1">
                    <img class="img-profile rounded-circle" src="{{URL::asset('img/logouft.png')}}" alt="Logo" height="70" width="70">
                </div>
                <div class="sidebar-brand-text mx-3">UFT</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="{{ route('home') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Tablero</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->


            <!-- Nav Item - Pages Collapse Menu -->
            @if (Auth::user()->rol_id == '0' || Auth::user()->rol_id == '2')
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Usuarios</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Listado:</h6>

                        <a class="collapse-item" href="{{ route('usuario/administrativo') }}">Director de Postgrado</a>
                        <a class="collapse-item" href="{{ route('usuario/directivo') }}">Coordinador</a>

                        <a class="collapse-item" href="{{ route('usuario/operativo') }}">Asistentes de Postgrado</a>
                    </div>
                </div>
            </li>
            @endif
            <!-- Nav Item - Utilities Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Estudiante</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('estudiantes.index') }}">Estudiantes</a>
                        <a class="collapse-item" href="{{ route('pensum') }}">Pensum</a>
                        <a class="collapse-item" href="{{ route('lapso.index') }}">Lapso</a>
                         <a class="collapse-item" href="{{ route('inscripcion') }}">Semestre</a>

                    </div>
                </div>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">



        </ul>
