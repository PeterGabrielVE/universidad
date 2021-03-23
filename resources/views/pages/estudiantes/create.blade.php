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
                                        <h6 class="m-0 font-weight-bold text-uft">Crear Estudiante</h6>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{{ url()->previous() }}" class="btn btn-uft" role="button"><i class="fas fa-arrow-left pr-2"></i>Atras </a>
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                        <div class="card-body">
                            <div class="row">
                                 <div class="col-lg-12">
                                    <div>
                                        {!! Form::open(['route'=>'estudiantes.store','method'=>'POST', 'class'=>'user']) !!}
                                            <div class="form-group row">
                                                <div class="col-sm-6 mb-3 mb-sm-0">
                                                    <input type="text" class="form-control" id="name" name="first_name" 
                                                        placeholder="Nombres">
                                                </div>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                                        placeholder="Apellidos">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                 <div class="col-sm-3 mb-3 mb-sm-0">
                                                    <input type="text" class="form-control" id="identification_card" name="identification_card" 
                                                    readonly
                                                    value="{{ $ced ?? null }}"
                                                        placeholder="Cédula">
                                                </div>
                                                <div class="col-sm-4">
                                                    <input type="email" class="form-control" id="email" name="email"
                                                    placeholder="Correo Electronico">
                                                </div>
                                                <div class="col-sm-2 mb-3 mb-sm-0 p-0">
                                                {!! Form::select('cod_phone', $prefijo,null, ['class'=>'form-control', 'id'=>'cod_phone']) !!}
                                                </div>
                                                <div class="col-sm-3 mb-3 mb-sm-0">
                                                    <input type="text" class="form-control
                                                    "
                                                        id="phone" name="phone" placeholder="Télefono">
                                                </div>
                                            </div>
                                            
                                            <div class="form-group row">
                                                 <div class="col-sm-6 mb-3 mb-sm-0">
                                                     {!! Form::label('lbl_pais', 'País donde vive:', ['class'=>'col-form-label s-12']) !!}
                                                    {!! Form::select('country_id', $paises, null, ['class'=>'form-control', 'id'=>'country_id']) !!}
                                                </div>
                                                <div class="col-sm-6">
                                                     {!! Form::label('lbl_equivalencia', 'Equivalencia:', ['class'=>'col-form-label s-12']) !!}
                                                    {!! Form::select('equivalency', ['0'=>'No','1'=>'Si'], null, ['class'=>'form-control', 'id'=>'equivalency']) !!}
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-4"></div>
                                                <div class="col-sm-4">
                                                    {!! Form::label('lbl_pais', 'Lapso:', ['class'=>'col-form-label s-12']) !!}
                                                    {!! Form::select('lapse_id', $lapso, null, ['class'=>'form-control', 'id'=>'lapse_id']) !!}
                                                  
                                                    <input type="hidden" class="form-control form-control-user"
                                                        id="status" 
                                                        name="status" 
                                                         value="Activo">       
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-3"></div>
                                                <div class="col-lg-3">
                                                   
                                                    <button type="submit" class="btn btn-uft btn-user btn-block" style="width: 400px;"><i class="icon-save mr-2"></i>Registrar</button>
                                                </div>
                                                <div class="col-lg-3"></div>
                                            </div>
                                            
                                       {!! Form::close() !!}
                                       
                                    </div>
                                </div>
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
