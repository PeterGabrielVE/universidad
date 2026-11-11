@extends('layouts.app')

{{-- $user es una UserAccount (dominio); $roles, $programs y $statuses vienen de UserController::formOptions. --}}
@section('content')
<div id="wrapper">
        @include('layouts.sidebar')

        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                 @include('layouts.navbar')

                <div class="container-fluid">
                    <h1 class="h3 mb-2 text-gray-800">Usuarios</h1>

                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h6 class="m-0 font-weight-bold text-uft">Editar usuario</h6>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{{route('user.index')}}" class="btn btn-uft" role="button"><i class="fas fa-arrow-left pr-2"></i>Atrás</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            {!! Form::open(['route'=>["user.update",$user->id()],'method'=>'PUT', 'class'=>'user']) !!}
                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <div class="form-row">
                                    <div class="col-md-6">
                                        {!! Form::label('name', 'Nombre', ['class'=>'col-form-label s-12']) !!}
                                        {!! Form::text('name', old('name', $user->name()), ['class'=>'form-control r-0 light s-12', 'id'=>'name', 'required']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::label('last_name', 'Apellido', ['class'=>'col-form-label s-12']) !!}
                                        {!! Form::text('last_name', old('last_name', $user->lastName()), ['class'=>'form-control r-0 light s-12', 'id'=>'last_name', 'required']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::label('identification_card', 'Cédula', ['class'=>'col-form-label s-12']) !!}
                                        {!! Form::number('identification_card', old('identification_card', $user->identificationCard()), ['class'=>'form-control r-0 light s-12', 'id'=>'identification_card', 'required', 'min'=>1]) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::label('email', 'Correo electrónico', ['class'=>'col-form-label s-12']) !!}
                                        {!! Form::email('email', old('email', $user->email()->value), ['class'=>'form-control r-0 light s-12', 'id'=>'email', 'required']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::label('phone', 'Teléfono', ['class'=>'col-form-label s-12']) !!}
                                        {!! Form::text('phone', old('phone', $user->phone()), ['class'=>'form-control r-0 light s-12', 'id'=>'phone']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::label('role', 'Rol', ['class'=>'col-form-label s-12']) !!}
                                        {!! Form::select('role', array_combine($roles, $roles), old('role', $user->role()), ['class'=>'form-control r-0 light s-12', 'id'=>'role', 'required']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::label('program_ids', 'Programas (Ctrl/Cmd para elegir varios)', ['class'=>'col-form-label s-12']) !!}
                                        {!! Form::select('program_ids[]', $programs, old('program_ids', $user->programIds()), ['class'=>'form-control r-0 light s-12', 'id'=>'program_ids', 'multiple', 'size'=>min(max(count($programs), 2), 6)]) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::label('status', 'Estado de la cuenta', ['class'=>'col-form-label s-12']) !!}
                                        {!! Form::select('status', array_combine($statuses, array_map('ucfirst', $statuses)), old('status', $user->status()->value), ['class'=>'form-control r-0 light s-12', 'id'=>'status']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::label('password', 'Nueva contraseña (opcional)', ['class'=>'col-form-label s-12']) !!}
                                        {!! Form::password('password', ['class'=>'form-control r-0 light s-12', 'id'=>'password', 'autocomplete'=>'new-password', 'minlength'=>8]) !!}
                                    </div>
                                </div>
                                <div class="form-group row mt-4">
                                    <div class="col-lg-4 offset-lg-4">
                                        <button type="submit" class="btn btn-uft btn-user btn-block"><i class="icon-save mr-2"></i>Actualizar</button>
                                    </div>
                                </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
