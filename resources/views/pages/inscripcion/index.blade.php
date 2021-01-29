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

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">Inscripción</h1>
                        
                    </div>


                    <div class="row">

                        <!-- Content Column -->
                        <div class="col-lg-12 mb-4">

                            <!-- Project Card Example -->
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Lapso de Estudiante:</h6>
                                </div>
                                <div class="card-body">
                                   <form action="{{ route('buscar.estudiante') }}" method="POST">
                                     @csrf
                                    <div class="form-inline">
                                      <label for="name">Buscar estudiante: </label> <input type="search" class="form-control ml-2 mr-2 col-2" name="consulta">

                                      <input  type="submit" value="Buscar">

                                    </div>
                                </form>
                                 <div class="content">
                                    <ul class="nav nav-tabs">
                                      <li class="active"><a data-toggle="tab" href="#home">Home</a></li>
                                      <li><a data-toggle="tab" href="#menu1">Menu 1</a></li>
                                      <li><a data-toggle="tab" href="#menu2">Menu 2</a></li>
                                    </ul>

                                    <div class="tab-content">
                                      <div id="home" class="tab-pane fade in active">
                                        <h3>HOME</h3>
                                        <p>Some content.</p>
                                      </div>
                                      <div id="menu1" class="tab-pane fade">
                                        <h3>Menu 1</h3>
                                        <p>Some content in menu 1.</p>
                                      </div>
                                      <div id="menu2" class="tab-pane fade">
                                        <h3>Menu 2</h3>
                                        <p>Some content in menu 2.</p>
                                      </div>
                                    </div>
                                 </div>         
                                    
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-6 mb-4">


                        </div>
                    </div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('layouts.footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
@endsection
