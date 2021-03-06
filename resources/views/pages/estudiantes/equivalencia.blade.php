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
                    <h1 class="h3 mb-2 text-gray-800">Inscripción</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h6 class="m-0 font-weight-bold text-uft">Estudiante: {{ $estudiante->first_name }} {{ $estudiante->last_name }}</h6>
                                    </div>
                                    
                                </div>
                            </div>
                           
                        </div>
                        <div class="card-body">
                             <div class="table-responsive">
                      <table class="table table-bordered" id="dataTable" width="100%" data-page-length="70" cellspacing="0" >
                          <thead>
                              <tr>
                                  <th>Codigo</th>
                                  <th>Nombre</th>
                                  <th>UC</th>
                                  <th>Semestre</th>                           
                                  <th style="width:16%">Seleccionar</th>
                              </tr>
                            </thead>
                                                     
                            <tbody>
                                  @foreach ($asignaturas as $asig)
                                  <tr>
                                      <td>{{ $asig->cod ?? '' }}</td>
                                      <td>{{ $asig->name ?? '' }}</td>
                                      <td>{{ $asig->uc ?? '' }}</td>
                                      <td>{{ $asig->semester_id ?? '' }}</td>                       
                                      <td class="text-center">
                                        <input type="checkbox" name="asignatura_check_box_id" asignatura-checkbox="{{$asig->id }}">
                                              
                                       </td> 
                                      </tr
                                      @endforeach
                                   </tbody>
                              </table>
                                                  
                            </div>
                            <div class="text-center"> <a class="btn btn-uft" onclick="inscribir()">Inscribir</a></div>
                        </div>

                    </div>

                </div>
                <input type="hidden" name="asignatura_seleccionadas" id="asignatura_seleccionadas" >
                <input type="hidden" name="id" id="id_estudiante" value={{ $estudiante->id }}>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            @include('layouts.footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
    </div>
    <!-- End of Page Wrapper -->

    <script>
      function inscribir() {
        var all_asign_array = [];
            $("input:checkbox[name=asignatura_check_box_id]").each(function(i, obj){
               
                if ( ($(this).is(":checked") ) && ($(this).attr("asignatura-checkbox") !== 'unknown' ) ) {
                    all_asign_array.push($(this).attr("asignatura-checkbox"));
                }
            });
            $('#asignatura_seleccionadas').val(all_asign_array.toString());

            var id = $('#id_estudiante').val();
            var asignaturas = $('#asignatura_seleccionadas').val();
            let url='{{route('getEstudiante', ":id")}}'
                url = url.replace(':id',id);


            $.ajax({
            dataType: 'json',
            type: 'post',
            url: '{{ url("createLapsoEstudianteEquivalencia") }}'+ '/' + id,
            data:  {
                    "_token": "{{ csrf_token() }}",
                    "asignaturas": asignaturas
              },
            success: function(data) {
            window.location.href = `${url}`
            
            }
          });
      }
    </script>
@endsection
