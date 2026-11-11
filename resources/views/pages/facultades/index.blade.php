@extends('layouts.panel')

@section('title', 'Facultades')

@section('actions')
    <a href="{{ route('programas.index') }}" class="btn btn-uft"><i class="fas fa-arrow-left pr-2"></i>Programas</a>
@endsection

@section('panel')
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Facultad</th>
                            <th>Descripción</th>
                            <th class="text-right">Programas</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($faculties as $faculty)
                            <tr>
                                <td colspan="2" class="p-0">
                                    {!! Form::open(['route' => ['facultades.update', $faculty->id], 'method' => 'PUT', 'class' => 'd-flex p-2', 'id' => 'faculty-' . $faculty->id]) !!}
                                        {!! Form::text('name', $faculty->name, ['class' => 'form-control form-control-sm mr-2', 'required', 'aria-label' => 'Nombre']) !!}
                                        {!! Form::text('description', $faculty->description, ['class' => 'form-control form-control-sm', 'aria-label' => 'Descripción']) !!}
                                    {!! Form::close() !!}
                                </td>
                                <td class="text-right">{{ $faculty->programs_count }}</td>
                                <td class="text-nowrap">
                                    <button type="submit" form="faculty-{{ $faculty->id }}" class="btn btn-uft btn-sm" title="Guardar"><i class="fas fa-save"></i></button>
                                    @if ($faculty->programs_count === 0)
                                        <form action="{{ route('facultades.destroy', $faculty->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('¿Eliminar «{{ $faculty->name }}»?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-uft btn-sm" title="Eliminar"><i class="fas fa-trash"></i></button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">Aún no hay facultades.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-uft">Nueva facultad</h6></div>
        <div class="card-body">
            {!! Form::open(['route' => 'facultades.store', 'method' => 'POST']) !!}
                <div class="form-row">
                    <div class="form-group col-md-5">
                        {!! Form::label('name', 'Nombre') !!}
                        {!! Form::text('name', old('name'), ['class' => 'form-control', 'required', 'maxlength' => 255]) !!}
                    </div>
                    <div class="form-group col-md-7">
                        {!! Form::label('description', 'Descripción') !!}
                        {!! Form::text('description', old('description'), ['class' => 'form-control']) !!}
                    </div>
                </div>
                <button type="submit" class="btn btn-uft"><i class="fas fa-save mr-2"></i>Crear facultad</button>
            {!! Form::close() !!}
        </div>
    </div>
@endsection
