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
                    <h1 class="h3 mb-2 text-gray-800">Usuarios</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h6 class="m-0 font-weight-bold text-uft">Editar Usuario</h6>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{{route('user.index')}}" class="btn btn-uft" role="button"><i class="fas fa-arrow-left pr-2"></i>Atras </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                 <div class="col-lg-12">
                                    <div>
                                        {!! Form::open(['route'=>["user.update",$user->id],'method'=>'PUT', 'class'=>'user']) !!}
                                         <div class="form-row">
                                        <div class="col-md-6">
                                            <div class="form-group m-0 has-feedback" id="campo_group">

                                                {!! Form::label('nombre', 'Nombre', ['class'=>'col-form-label s-12']) !!}
                                                {!! Form::text('name', $user->name ?? '', ['class'=>'form-control r-0 light s-12', 'id'=>'name', 'onclick'=>'inputClear(this.id)']) !!}
                                                <span class="campo_span"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group m-0" id="modulo_group">
                                                {!! Form::label('roles', 'Perfil', ['class'=>'col-form-label s-12']) !!}
                                                {!! Form::select('rol_id', $roles, $user->rol_id ?? '', ['class'=>'form-control r-0 light s-12', 'id'=>'rol_id', 'onclick'=>'inputClear(this.id)']) !!}
                                                <span class="status_span"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group m-0 has-feedback" id="campo_group">

                                                {!! Form::label('metaListValue', 'Correo Electronico', ['class'=>'col-form-label s-12']) !!}
                                                {!! Form::email('email', $user->email ?? '', ['class'=>'form-control r-0 light s-12', 'id'=>'email', 'onclick'=>'inputClear(this.id)']) !!}
                                                <span class="campo_span"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group m-0 has-feedback" id="campo_group">

                                                {!! Form::label('Contraseña', 'Contraseña', ['class'=>'col-form-label s-12']) !!}
                                                {!! Form::text('password', null, ['class'=>'form-control r-0 light s-12', 'id'=>'password', 'onclick'=>'inputClear(this.id)']) !!}
                                                <span class="campo_span"></span>
                                            </div>
                                        </div>


                                    </div>
                                    <div class="form-group row mt-4">
                                                <div class="col-lg-3"></div>
                                                <div class="col-lg-3">

                                                    <button type="submit" class="btn btn-uft btn-user btn-block" style="width: 400px;"><i class="icon-save mr-2"></i>Actualizar</button>
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
