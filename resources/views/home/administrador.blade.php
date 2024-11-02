@extends('layouts.app')

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
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header"
                                        style="background-color: rgba(51, 105, 232, 0.1)">
                                        <center><i class = "fa fa-home" style="color:#3369e8"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Login
                                            <div class="pull-right badge" id="WrControls"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header"
                                        style="background-color:rgba(187, 120, 36, 0.1) ">
                                        <center><i class = "fa fa-users" style="color:#BB7824"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Roles
                                            <div class="pull-right badge" id="WrControls"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header"
                                        style="background-color:rgba(187, 120, 36, 0.1) ">
                                        <center><i class = "fa fa-cubes" style="color:#BB7824"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Usuarios
                                            <div class="pull-right badge" id="WrControls"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header"
                                        style="background-color: rgba(22, 160, 133, 0.1)">
                                        <center><i class = "fa fa-cubes" style="color:#16A085"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Configuración
                                            <div class="pull-right badge" id="WrControls"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header"
                                        style="background-color:  rgba(213, 15, 37, 0.1)">
                                        <center><i class="fa fa-cubes" style="color:#d50f25"> </i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Administrativo
                                            <div class="pull-right badge" id="WrForms"></div>
                                        </h4>
                                    </div>

                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header"
                                        style="background-color:  rgba(51, 105, 232, 0.1)">
                                        <center><i class="fa fa-table" style="color:#3369e8"> </i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Docentes
                                            <div class="pull-right badge" id="WrGridSystem"></div>
                                        </h4>
                                    </div>

                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color:  rgba(250, 188, 9, 0.1)">
                                        <center><i class="fa fa-info-circle" style="color:#fabc09"> </i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Estudiantes
                                            <div class="pull-right badge" id="WrInformation"></div>
                                        </h4>
                                    </div>

                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(121, 90, 71, 0.1)">
                                        <center><i class="fa fa-bars" style="color:#795a47"> </i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>PPFF
                                            <div class="pull-right badge" id="WrNavigation"></div>
                                        </h4>
                                    </div>

                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Grados
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Paralelos
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Asignación
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Materia
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Inscripción
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Pagos
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Kardex
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Calificación
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Gestión
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Niveles
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Turno
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Permisos
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Reportes
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-3 col-sm-4">
                            <div class="wrimagecard wrimagecard-topimage">
                                <a href="#">
                                    <div class="wrimagecard-topimage_header" style="background-color: rgba(130, 93, 9, 0.1)">
                                        <center><i class="fa fa-magic" style="color:#825d09"></i></center>
                                    </div>
                                    <div class="wrimagecard-topimage_title">
                                        <h4>Instituciones
                                            <div class="pull-right badge" id="WrThemesIcons"></div>
                                        </h4>
                                    </div>
                                </a>
                            </div>
                        </div>

                    </div>


                </div>
            </div>

        </div>


    </div>
    <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->
@endsection
