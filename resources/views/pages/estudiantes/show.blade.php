@extends('layouts.panel')

{{-- Expediente del estudiante (#14, FUNCTIONAL_SPECIFICATIONS.md §19).
     Identidad y programas: solo lectura aquí (se editan en la gestión de usuarios).
     Contacto: el propio estudiante o el personal con students.manage.
     Sede, equivalencia y lapso de ingreso: solo el personal. --}}
@php($user = $student->user)

@section('title')
    {{ $user?->name }} {{ $user?->last_name }}
@endsection

@section('actions')
    <a href="{{ route('estudiantes.kardex', $student->id) }}" class="btn btn-uft"><i class="fas fa-id-card pr-2"></i>Kardex</a>
    @if ($canEditIdentity && $user)
        <a href="{{ route('user.edit', $user->id) }}" class="btn btn-outline-secondary"><i class="fas fa-user-cog pr-2"></i>Identidad y programas</a>
    @endif
@endsection

@section('panel')
    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-uft">Identidad</h6></div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Cédula</dt><dd class="col-sm-9">{{ $user?->identification_card }}</dd>
                <dt class="col-sm-3">Correo</dt><dd class="col-sm-9">{{ $user?->email }}</dd>
                <dt class="col-sm-3">Programas</dt><dd class="col-sm-9">{{ $user?->programs->pluck('name')->join(', ') ?: 'Sin programa' }}</dd>
                <dt class="col-sm-3">Lapso de ingreso</dt><dd class="col-sm-9">{{ $admissionLapse?->name ?? '—' }}</dd>
                <dt class="col-sm-3">Sede</dt><dd class="col-sm-9">{{ $campus?->name ?? '—' }}</dd>
                <dt class="col-sm-3">Equivalencia</dt><dd class="col-sm-9">{{ $student->equivalency ?: '—' }}</dd>
            </dl>
            @if ($isOwner)
                <p class="small text-muted mt-3 mb-0">
                    Para corregir tu nombre, cédula, correo o programa, solicítalo a Control de Estudios.
                </p>
            @endif
        </div>
    </div>

    @if ($canEditContact)
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-uft">{{ $canManage ? 'Contacto y datos administrativos' : 'Mis datos de contacto' }}</h6>
            </div>
            <div class="card-body">
                {!! Form::open(['route' => ['estudiantes.update', $student->id], 'method' => 'PUT']) !!}
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            {!! Form::label('phone', 'Teléfono') !!}
                            {!! Form::text('phone', old('phone', $user?->phone), ['class' => 'form-control', 'maxlength' => 30, 'placeholder' => '+58 414 1234567', 'autocomplete' => 'tel']) !!}
                        </div>
                        <div class="form-group col-md-6">
                            {!! Form::label('country_id', 'País') !!}
                            {!! Form::select('country_id', $countries, old('country_id', $user?->country_id), ['class' => 'form-control', 'placeholder' => 'Seleccione']) !!}
                        </div>

                        @if ($canManage)
                            <div class="form-group col-md-4">
                                {!! Form::label('sede_id', 'Sede') !!}
                                {!! Form::select('sede_id', $campuses, old('sede_id', $user?->sede_id), ['class' => 'form-control', 'placeholder' => 'Sin sede']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('admission_lapse_id', 'Lapso de ingreso') !!}
                                {!! Form::select('admission_lapse_id', $lapses, old('admission_lapse_id', $student->lapse_id), ['class' => 'form-control', 'placeholder' => 'Sin lapso']) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('equivalency', 'Ingresó por equivalencia') !!}
                                {!! Form::select('equivalency', ['0' => 'No', '1' => 'Sí'], old('equivalency', $student->equivalency === 'Sí' ? '1' : '0'), ['class' => 'form-control']) !!}
                            </div>
                        @endif
                    </div>
                    <button type="submit" class="btn btn-uft"><i class="fas fa-save mr-2"></i>Guardar</button>
                {!! Form::close() !!}
            </div>
        </div>
    @endif
@endsection
