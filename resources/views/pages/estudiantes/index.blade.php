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
                    <h1 class="h3 mb-2 text-gray-800">Estudiantes</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h6 class="m-0 font-weight-bold text-uft">Listado Estudiantes</h6>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{{ route('estudiantes.create') }}" class="btn btn-uft" role="button"><i class="fas fa-plus pr-2"></i>Agregar Estudiante</a>
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Cedula</th>
                                            <th>Nombre Completo</th>
                                            <th>Telefono</th>
                                            <th>País</th>
                                            <th>Lapso</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Cedula</th>
                                            <th>Nombre Completo</th>
                                            <th>Telefono</th>
                                            <th>País</th>
                                            <th>Lapso</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($estudiantes as $estudiante)
                                        <tr>
                                            <td>{{ $estudiante->identification_card ?? '' }}</td>
                                            <td>{{ $estudiante->first_name ?? '' }} {{ $estudiante->last_name ?? '' }}</td>
                                            <td>{{ $estudiante->phone ?? '' }}</td>
                                            <td>{{ $estudiante->name }}</td>
                                            <td>{{ $estudiante->detail }}</td>
                                            <td>
                                                <form action="{{ route('estudiantes.destroy',$estudiante->id) }}" method="POST">
                                   
                                                    <a class="btn btn-info" href="{{ route('estudiantes.show',$estudiante->id) }}">Show</a>
                                    
                                                    <a class="btn btn-primary" href="{{ route('estudiantes.edit',$estudiante->id) }}">Edit</a>
                                   
                                                    @csrf
                                                    @method('DELETE')
                                      
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                       
                                    </tbody>
                                </table>
                                {!! $estudiantes->links() !!}
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
