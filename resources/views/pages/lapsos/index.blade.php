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
                    <h1 class="h3 mb-2 text-gray-800">Lapsos Acádemicos</h1>

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
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Final</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Lapso</th>
                                            <th>Fecha Inicio</th>
                                            <th>Fecha Final</th>
                                            <th>Opciones</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($lapsos as $lapso)
                                        <tr>
                                            <td>{{ $lapso->name }}</td>
                                            <td>{{ $lapso->start_lapse }}</td>
                                            <td>{{ $lapso->end_lapse }}</td>
                                            <td>
                                                @if($lapso->status=='1')
                                                <form action="{{ route('lapso.destroy',$lapso->id) }}" method="POST">
                                   
                                                  
                                    
                                                    <a class="btn btn-uft" onclick="editLapso({{ $lapso->id }})"><i class="fas fa-edit"></i></a>
                                   
                                                    @csrf
                                                    @method('DELETE')
                                      
                                                    <button type="submit" class="btn btn-uft"><i class="fas fa-trash"></i></button>
                                                </form>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                       
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
              $('#edit_status').val(data.status);
              
            }
          });
  }
</script>
@endsection