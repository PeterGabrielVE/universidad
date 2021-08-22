<ul class="navbar-nav bg-white sidebar sidebar-uft accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center mb-4 mt-2" href="{{ route('home') }}">
                <div class="row text-center">
                    <div class="col-12 mt-4">
                        <div class="sidebar-brand-icon">
                            <img class="img-profile rounded-circle" src="{{URL::asset('img/logo.png')}}" alt="Logo" height="70" width="70">
                        </div>
                    </div>
                    <div class="col-12 pt-2">
                        <div class="sidebar-brand-text row text-uft">UFT</div>
                    </div>
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link text-uft" href="{{ route('home') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Principal</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <!-- Nav Item - Pages Collapse Menu -->
            @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 5)
            <li class="nav-item">
                <a class="nav-link collapsed text-uft" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Usuarios</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-uft py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('usuario/administrativo') }}">Director de Postgrado</a>
                        <a class="collapse-item" href="{{ route('usuario/directivo') }}">Coordinador</a>
                        <a class="collapse-item" href="{{ route('usuario/operativo') }}">Asistentes de Postgrado</a>
                        <a class="collapse-item" href="{{ route('estudiantes.index') }}">Estudiantes</a>
                    </div>
                </div>
            </li>
            @endif
            @if (Auth::user()->rol_id == 2 || Auth::user()->rol_id == 3)
            <li class="nav-item">
                <a class="nav-link collapsed text-uft" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Usuarios</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-uft py-2 collapse-inner rounded">
                        <a class="collapse-item" href="{{ route('estudiantes.index') }}">Estudiantes</a>
                    </div>
                </div>
            </li>
             <!-- Divider -->
             <hr class="sidebar-divider my-0">
            @endif

             @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 5)
             <li class="nav-item">
                 <a class="nav-link collapsed text-uft" href="{{ route('reports') }}">
                     <i class="fas fa-fw fa-file"></i>
                     <span>Reportes</span>
                 </a>
             </li>
             <hr class="sidebar-divider">
             @endif
            <!-- Nav Item - Utilities Collapse Menu -->
            @if (Auth::user()->rol_id == 1 || Auth::user()->rol_id == 5)
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
            @endif

            @if (Auth::user()->rol_id == 4)
            <li class="nav-item">
                <a class="nav-link collapsed text-uft" href="{{ route('estudiantes.show',Auth::user()->estudiante(Auth::user()->id)) }}">
                    <span>Mi perfil</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <li class="nav-item">
                <a class="nav-link collapsed text-uft" href="{{ route('estudiantes.document',Auth::user()->estudiante(Auth::user()->id)) }}">
                    <span>Mis Documentos</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            @endif
        </ul>


