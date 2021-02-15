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
        @include('pages.inscripcion.edit')
        <!-- Topbar -->
        @include('layouts.navbar')
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

        <!-- Page Heading -->
          <div class="d-sm-flex align-items-center justify-content-between mb-4">
              <h1 class="h3 mb-0 text-gray-800">Semestre</h1>
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
                      <label for="name">Buscar estudiante: </label> <input type="search" class="form-control ml-2 mr-2 col-2" name="consulta" placeholder="Ingrese Cédula">

                      <input  type="submit" value="Buscar">
                    </div>
                    </form>
                    @if(isset($estudiante))
                      <div class="content">
                          <ul class="nav nav-tabs">
                            <li class="active m-2"><a data-toggle="tab" href="#home">Información</a></li>
                            <li class="m-2"><a data-toggle="tab" href="#menu1">Carga Academica</a></li>
                            <li class="m-2"><a data-toggle="tab" href="#menu2">Historial</a></li>
                          </ul>

                          <div class="tab-content">
                              <div id="home" class="tab-pane active">
                              <h4 class="mt-2">{{ $estudiante->first_name }} {{ $estudiante->last_name }}</h4>
                                <div class="card-body">
                                <div class="row">
                                <div class="col-lg-12">
                                       <div>
                                        <div class="form-group row">
                                          <div class="col-sm-4 mb-3 mb-sm-0">
                                          <p><label for="pensamiento_id">Cedula:</label>
                                           <b>{{ $estudiante->identification_card ?? '' }}</b></p>
                                          </div>
                                          <div class="col-sm-4">
                                          <p><label for="pensamiento_id">Correo:</label>
                                          <b>{{ $estudiante->email  ?? ''}}</b></p>
                                          </div>
                                          <div class="col-sm-4 mb-3 mb-sm-0">
                                          <p><label for="pensamiento_id">Telefono:</label>
                                          <b>{{ $estudiante->phone  ?? ''}}</b></p>
                                          </div>
                                                               
                                        </div>
            <div class="form-group row">
              <div class="col-sm-4 mb-3 mb-sm-0">
                <p><label for="pensamiento_id">Ingreso:</label>
                 <b>{{ $estudiante->lapsos->name ?? '' }}</b></p>
              </div>
            <div class="col-sm-4">
                <p><label for="pensamiento_id">País:</label>
                <b>{{ $estudiante->pais->name  ?? ''}}</b></p>
            </div>
            <div class="col-sm-4 mb-3 mb-sm-0">
              <p><label for="pensamiento_id">Equivalencia:</label>
                @if($estudiante->equivalency == '1')
                <b>Sí</b>
                @else
                <b>No</b>
                @endif
              </p>
          </div>
          <div class="col-sm-4"
            <p><label for="pensamiento_id">Estatus:</label>
             <b>{{ $estudiante->status ?? ''}}</b>
           </p>
         </div>
                               
        </div>
                                                        
      </div>
    </div>
              </div>
            </div>
          </div>
          <div id="menu1" class="tab-pane fade">
            <h3>Carga Academica</h3>
            @if($lapso->status =='0')
            <div class="pull-right">
              <a href="{{ route('createInscripcion',$estudiante->id) }}" class="btn btn-uft"><i class="fas fa-plus pr-2"></i>Inscribir</a>
            </div>
            @endif
            <div class="card-body">
                <div class="table-responsive">
                      <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                          <thead>
                              <tr>
                                  <th>Materia</th>
                                  <th>Nota</th>
                                  <th>Estatus</th>
                                                              
                                  <th style="width:16%">Actualizar</th>
                              </tr>
                            </thead>
                                                     
                            <tbody>
                                  @foreach ($carga as $c)
                                  <tr>
                                      <td>{{ $c->asignatura->name ?? '' }}</td>
                                      <td>{{ $c->note ?? '' }}</td>
                                      <td>{{ $c->status ?? '' }}</td>
                                                             
                                      <td class="text-center">
                                        <a class="btn btn-uft btn-sm" onclick="editarCarga({{ $c->id }})"><i class="fas fa-edit"></i></a>
                                                    
                                       </td>
                                      </tr
                                      @endforeach
                                   </tbody>
                              </table>
                                                  
                            </div>
                          </div>
                      </div>
                      <div id="menu2" class="tab-pane fade">
                      <h3>Historial</h3>
                         <div class="card-body">
                <div class="table-responsive">
                      <table class="table table-bordered" id="dataTable2" width="100%" cellspacing="0">
                          <thead>
                              <tr>
                                  <th>Materia</th>
                                  <th>Nota</th>
                                  <th>Estatus</th>
                                   
                              </tr>
                            </thead>
                                                     
                            <tbody>
                                  @foreach ($historia as $c)
                                  <tr>
                                      <td>{{ $c->asignatura->name ?? '' }}</td>
                                      <td>{{ $c->note ?? '' }}</td>
                                      <td>{{ $c->status ?? '' }}</td>
                                                             
                                      
                                      </tr
                                      @endforeach
                                   </tbody>
                              </table>
                                                  
                            </div>
                          </div>               
                      </div>
                  </div>
              </div>         
            @endif    
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
@section('js')
<script>
 
  function editarCarga(id){
    $("#modal_semestre").modal("show");
        
       var url ="{{url('getLapso')}}/"+id;
          $.ajax({
            type : 'get',
            url  : url,
            data : {'id':id},
            success:function(data){
             
              $('#course').val(data['0'].course_id);
              $('#id').val(data['0'].id);
              $('#note').val(data['0'].note);
              console.log(data);
              
            }
          });
  }
 
</script>
@endsection