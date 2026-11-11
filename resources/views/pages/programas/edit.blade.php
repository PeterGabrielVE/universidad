@extends('layouts.panel')

@section('title', 'Editar programa')

@section('actions')
    <a href="{{ route('programas.index') }}" class="btn btn-uft"><i class="fas fa-arrow-left pr-2"></i>Programas</a>
@endsection

@section('panel')
    <div class="card shadow mb-4">
        <div class="card-body">
            {!! Form::open(['route' => ['programas.update', $program->id], 'method' => 'PUT']) !!}
                <div class="form-row">
                    <div class="form-group col-md-5">
                        {!! Form::label('name', 'Nombre') !!}
                        {!! Form::text('name', old('name', $program->name), ['class' => 'form-control', 'required', 'maxlength' => 255]) !!}
                    </div>
                    <div class="form-group col-md-4">
                        {!! Form::label('faculty_id', 'Facultad') !!}
                        {!! Form::select('faculty_id', $faculties, old('faculty_id', $program->faculty_id), ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione']) !!}
                    </div>
                    <div class="form-group col-md-3">
                        {!! Form::label('min_passing_grade', 'Nota mínima (0–100)') !!}
                        {!! Form::number('min_passing_grade', old('min_passing_grade', $program->min_passing_grade), ['class' => 'form-control', 'required', 'min' => 0, 'max' => 100, 'step' => '0.01']) !!}
                        <small class="form-text text-muted">Aplica a los cierres futuros; los lapsos ya cerrados conservan su resultado.</small>
                    </div>
                    <div class="form-group col-12">
                        {!! Form::label('description', 'Descripción') !!}
                        {!! Form::textarea('description', old('description', $program->description), ['class' => 'form-control', 'rows' => 3]) !!}
                    </div>
                </div>
                <button type="submit" class="btn btn-uft"><i class="fas fa-save mr-2"></i>Guardar</button>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
