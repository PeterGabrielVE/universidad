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
                                @if(isset($estudiante))
                                 <div class="content">
                                    <ul class="nav nav-tabs">
                                      <li class="active"><a data-toggle="tab" href="#home">Información</a></li>
                                      <li><a data-toggle="tab" href="#menu1">Carga Academica</a></li>
                                      <li><a data-toggle="tab" href="#menu2">Historial</a></li>
                                    </ul>

                                    <div class="tab-content">
                                      <div id="home" class="tab-pane active">
                                        <h3>HOME</h3>
                                                         <div class="card-body">
                                                          <div class="row">
                                                               <div class="col-lg-12">
                                                                  <div>
                                                      
                                                            <div class="form-group row">
                                                                <div class="col-sm-6 mb-3 mb-sm-0">
                                                                    <input type="text" class="form-control form-control-user" id="name" name="first_name" 
                                                                        placeholder="Nombres" value="{{ $estudiante->first_name }}">
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <input type="text" class="form-control form-control-user" id="last_name" name="last_name" 
                                                                        placeholder="Apellidos" value="{{ $estudiante->last_name }}">
                                                                </div>
                                                            </div>
                                                            <div class="form-group row">
                                                                 <div class="col-sm-6 mb-3 mb-sm-0">
                                                                    <input type="text" class="form-control form-control-user" id="identification_card" name="identification_card" 
                                                                        placeholder="Cédula">
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <input type="email" class="form-control form-control-user" id="email" name="email"
                                                                    placeholder="Correo Electronico">
                                                                </div>
                                                            </div>
                                                            
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                      </div>
                                      <div id="menu1" class="tab-pane fade">
                                        <h3>Carga Academica</h3>
                                        <div class="card-body">
                                              <div class="table-responsive">
                                                  <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                      <thead>
                                                          <tr>
                                                              <th>Materia</th>
                                                              <th>Nota</th>
                                                              <th>Estatus</th>
                                                              
                                                              <th style="width:16%">Opciones</th>
                                                          </tr>
                                                      </thead>
                                                     
                                                      <tbody>
                                                          @foreach ($carga as $c)
                                                          <tr>
                                                              
                                                              <td>{{ $c->course_id ?? '' }}</td>
                                                              <td>{{ $c->note ?? '' }}</td>
                                                              <td>{{ $c->status ?? '' }}</td>
                                                             
                                                              <td>
                                                                  <form action="{{ route('estudiantes.destroy',$c->id) }}" method="POST">
                                                     
                                                                      <a class="btn btn-uft btn-sm" href="{{ route('estudiantes.show',$c->id) }}"><i class="fas fa-eye"></i></a>
                                                      
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
                                                  
                                              </div>
                                          </div>
                                      </div>
                                      <div id="menu2" class="tab-pane fade">
                                        <h3>Historial</h3>
                                        <p>Some content in menu 2.</p>
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
