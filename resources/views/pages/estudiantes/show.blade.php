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
                    <h1 class="h3 mb-2 text-gray-800">{{ Auth::user()->name ?? null }} {{ Auth::user()->last_name ?? null }}</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h6 class="m-0 font-weight-bold text-uft">Mis Datos</h6>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{{route('estudiantes.index')}}" class="btn btn-uft" role="button"><i class="fas fa-arrow-left pr-2"></i>Atras </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                 <div class="col-lg-12">
                                    <div>
                                        {!! Form::open(['route'=>["estudiantes.update",$estudiante->id],'method'=>'PUT', 'class'=>'user']) !!}
                                            <div class="form-group row">
                                                <div class="col-sm-6 mb-3 mb-sm-0">
                                                    {!! Form::label('lbl_nombres', 'Nombres:', ['class'=>'col-form-label s-12']) !!}
                                                    {!! Form::text('first_name', $user->name ?? null, ['class'=>'form-control form-control-user', 'id'=>'first_name','placeholder'=>'Nombres']) !!}

                                                </div>
                                                <div class="col-sm-6">
                                                     {!! Form::label('lbl_apellidos', 'Apellido:', ['class'=>'col-form-label s-12']) !!}
                                                     {!! Form::text('last_name', $user->last_name ?? null, ['class'=>'form-control form-control-user', 'id'=>'last_name','placeholder'=>'Apellidos']) !!}
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                 <div class="col-sm-6 mb-3 mb-sm-0">
                                                    {!! Form::label('lbl_cedula', 'Cedula:', ['class'=>'col-form-label s-12']) !!}
                                                    {!! Form::number('identification_card', $user->identification_card ?? null, ['class'=>'form-control form-control-user', 'id'=>'identification_card','placeholder'=>'Cedula']) !!}
                                                </div>
                                                <div class="col-sm-6">
                                                    {!! Form::label('lbl_email', 'Email:', ['class'=>'col-form-label s-12']) !!}
                                                    {!! Form::email('email', $user->email ?? null, ['class'=>'form-control form-control-user', 'id'=>'email','placeholder'=>'Correo Electronico']) !!}

                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-1 mb-3 ml-4 p-0">
                                                    {!! Form::label('lbl_codigo', 'Codigo:', ['class'=>'col-form-label s-12']) !!}
                                                    {!! Form::select('cod_phone', $prefijo,$user->cod_phone ?? '', ['class'=>'form-control', 'id'=>'cod_phone']) !!}
                                                </div>
                                                <div class="col-sm-4 mb-3 mb-sm-0">
                                                    {!! Form::label('lbl_telefono', 'Telefono:', ['class'=>'col-form-label s-12']) !!}
                                                    {!! Form::text('phone', $user->phone ?? null, ['class'=>'form-control ', 'id'=>'phone','placeholder'=>'Télefono']) !!}
                                                 </div>
                                                 <div class="col-sm-6">
                                                    {!! Form::label('lbl_lapso', 'Lapso:', ['class'=>'col-form-label s-12']) !!}
                                                      <input type="text" class="form-control form-control-user"
                                                          id="lapse_name" placeholder="Lapso de Ingreso" value="{{ $lapso->name }}" readonly="true">
                                                      <input type="hidden" class="form-control form-control-user"
                                                          id="lapse_id"
                                                          name="lapse_id"
                                                          placeholder="Lapso de Ingreso" value="{{ $lapso->id }}">
                                                      <input type="hidden" class="form-control form-control-user"
                                                          id="status"
                                                          name="status"
                                                           value="Activo">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6 mb-3 mb-sm-0">
                                                    {!! Form::label('lbl_pais', 'Pais:', ['class'=>'col-form-label s-12']) !!}
                                                    {!! Form::select('country_id', $paises,$user->country_id ?? null, ['class'=>'form-control', 'id'=>'country_id']) !!}
                                                </div>
                                                <div class="col-sm-6">
                                                    {!! Form::label('lbl_Doctorado', 'Doctorado:', ['class'=>'col-form-label s-12']) !!}
                                                   {!! Form::select('doctorado_id', $doctorados, $user->doctorado_id ?? null, ['class'=>'form-control', 'id'=>'doctorado']) !!}
                                               </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-3"></div>
                                                <div class="col-lg-3">
                                                    <button type="submit" class="btn btn-uft btn-block" style="width: 400px;"><i class="icon-save mr-2"></i>Editar</button>
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

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->
@endsection
