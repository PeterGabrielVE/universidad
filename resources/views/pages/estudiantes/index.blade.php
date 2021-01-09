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
                    <h1 class="h3 mb-2 text-gray-800">Tables</h1>
                    <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
                        For more information about DataTables, please visit the <a target="_blank"
                            href="https://datatables.net">official DataTables documentation</a>.</p>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">DataTables Example</h6>
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
                                        @foreach ($estudiantes as $product)
                                        <tr>
                                            <td>{{ ++$i }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->detail }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>{{ $product->detail }}</td>
                                            <td>
                                                <form action="{{ route('estudiantes.destroy',$product->id) }}" method="POST">
                                   
                                                    <a class="btn btn-info" href="{{ route('estudiantes.show',$product->id) }}">Show</a>
                                    
                                                    <a class="btn btn-primary" href="{{ route('estudiantes.edit',$product->id) }}">Edit</a>
                                   
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
