@extends('layouts.app')

@section('content')
<div id="wrapper">

        <!-- Sidebar -->
        @include('layouts.sidebar')
        <!-- End of Sidebar -->
        @include('pages.usuarios.create')
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
                                        <div class="row form-inline">
                                            <a data-toggle="modal" data-target="#create" class="btn btn-uft" role="button"><i class="fas fa-plus pr-2"></i>Crear Estudiante</a>
                                        </div>

                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nombres y Apellidos</th>
                                            <th>Cedula</th>
                                            <th>Telefono</th>
                                            <th>Correo Electronico</th>
                                            <th>Equivalencia</th>
                                            <th>País</th>
                                            <th style="width:25%">Lapso</th>
                                            <th style="width:16%">Opciones</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($estudiantes as $estudiante)
                                        <tr>

                                            <td>{{ $estudiante->first_name ?? '' }} {{ $estudiante->last_name ?? '' }}</td>
                                            <td>{{ $estudiante->identification_card ?? '' }}</td>
                                            <td>{{ $estudiante->prefijo->Prefijo ?? '' }}-{{ $estudiante->phone ?? '' }}</td>
                                            <td>{{ $estudiante->email ?? '' }}</td>
                                            <td>
                                            @if ($estudiante->equivalency == '1')
                                              Si
                                            @else
                                              No
                                            @endif
                                        </td>
                                            <td>{{ $estudiante->paises->name ?? '' }}</td>
                                            <td>{{ $estudiante->lapsos->name ?? '' }}</td>
                                            <td>
                                                <form action="{{ route('estudiantes.destroy',$estudiante->id) }}" method="POST">

                                                    <a class="btn btn-uft btn-sm" href="{{ route('estudiantes.edit',$estudiante->id) }}"><i class="fas fa-edit"></i></a>

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-uft btn-sm"><i class="fas fa-trash"></i></button>
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
<script type="text/javascript">
    function crear(){
        var cedula = $('#cedula').val();
        var url ="{{url('buscarEstudiantePorCedula')}}";

          $.ajax({
            type : 'GET',
            url  : url,
            data : {'cedula':cedula},
            success:function(data){
                console.log(data);
                if(data == 'null'){
                    Swal.fire('No existe un estudiante con esta cédula');

                    var url = {!! json_encode(url('crearEstudiante')) !!};
                    window.location.href = `${url}/${cedula}`;
                }else{
                    Swal.fire('Ya existe un estudiante con esta cédula');
                }
            }
          });

    }


</script>
@endsection
