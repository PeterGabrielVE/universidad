@extends('layouts.app')

@section('content')
{{-- modal create --}}
@include('pages.lapsos.create')
@include('pages.lapsos.edit')
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
                    <h1 class="h3 mb-2 text-gray-800">Pensum Acádemico</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h6 class="m-0 font-weight-bold text-uft">Asignaturas</h6>
                                    </div>
                                  
                                </div>
                            </div>
                           
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr> 
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>UC</th>
                                            <th>Semestre</th>
                                            <th>Requisitos</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Código</th>
                                            <th>Nombre</th>
                                            <th>UC</th>
                                            <th>Semestre</th>
                                            <th>Requisitos</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($asignaturas as $asig)
                                        <tr>
                                            <td>{{ $asig->cod ?? '' }}</td>
                                            <td>{{ $asig->name }}</td>
                                            <td>{{ $asig->uc ?? '' }}</td>
                                            <td>{{ $asig->semester_id ?? '' }}</td>
                                            <td>
                                               {{ $asig->required?? '' }}
                                            </td>
                                        </tr>
                                        @endforeach
                                       
                                    </tbody>
                                </table>
                                
                            </div>
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
