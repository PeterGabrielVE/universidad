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
                    <h1 class="h3 mb-2 text-gray-800">Lapsos académicos</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h6 class="m-0 font-weight-bold text-uft">Lapsos</h6>
                                    </div>
                                    <div class="pull-right">
                                        <a href="#" class="btn btn-uft" role="button" data-toggle="modal" data-target="#create"><i class="fas fa-plus pr-2"></i>Crear Nuevo Lapso</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Lapso</th>
                                            <th>Programa</th>
                                            <th>Fecha inicio</th>
                                            <th>Fecha final</th>
                                            <th>Estado</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($lapsos as $lapso)
                                        <tr>
                                            <td>{{ $lapso->name }}</td>
                                            <td>{{ $lapso->program?->name ?? 'Sin programa (anterior a la Fase 3)' }}</td>
                                            <td>{{ $lapso->start_lapse }}</td>
                                            <td>{{ $lapso->end_lapse }}</td>
                                            <td>{{ $lapso->status->label() }}</td>
                                            <td>
                                                <a class="btn btn-uft" href="{{ route('secciones.index', $lapso->id) }}" title="Secciones"><i class="fas fa-chalkboard-teacher"></i></a>
                                                {{-- Cerrado: inmutable. Solo se elimina un lapso que nunca se abrió. --}}
                                                @if ($lapso->status !== \App\Modules\Academic\Domain\LapseStatus::Cerrado)
                                                <a class="btn btn-uft" onclick="editLapso({{ $lapso->id }})" title="Editar"><i class="fas fa-edit"></i></a>
                                                @endif
                                                @if ($lapso->status === \App\Modules\Academic\Domain\LapseStatus::Planificacion)
                                                <form action="{{ route('lapso.destroy',$lapso->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-uft" title="Eliminar"><i class="fas fa-trash"></i></button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">No hay lapsos en tu alcance.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                {!! $lapsos->links() !!}
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
@section('js')
<script>
 function editLapso(id){
    $("#edit").modal("show");

        var url ="{{url('editLapso')}}/"+id;
          $.ajax({
            type : 'get',
            url  : url,
            data : {'id':id},
            success:function(data){
              console.log(data);
              $('#edit_start_lapse').val(data.start_lapse);
              $('#edit_end_lapse').val(data.end_lapse);
              $('#_id').val(data.id);
              $('#edit_status').val(data.status).data('original', data.status);
              $('#edit_errors').addClass('d-none').empty();

            }
          });
  }
</script>
@endsection
