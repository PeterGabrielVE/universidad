@extends('layouts.panel')

{{-- $programs: programas del alcance del usuario; $canManage: programs.manage. --}}
@section('title', 'Programas de postgrado')

@section('actions')
    @if ($canManage)
        <a href="{{ route('facultades.index') }}" class="btn btn-outline-secondary"><i class="fas fa-university pr-2"></i>Facultades</a>
    @endif
@endsection

@section('panel')
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Programa</th>
                            <th>Facultad</th>
                            <th class="text-right">Nota mínima</th>
                            <th class="text-right">Asignaturas</th>
                            <th class="text-right">Usuarios</th>
                            <th class="text-right">Lapsos</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($programs as $program)
                            <tr>
                                <td>{{ $program->name }}</td>
                                <td>{{ $program->faculty?->name ?? 'Sin facultad' }}</td>
                                <td class="text-right">{{ number_format($program->min_passing_grade, 2) }}</td>
                                <td class="text-right">{{ $program->courses_count }}</td>
                                <td class="text-right">{{ $program->users_count }}</td>
                                <td class="text-right">{{ $program->lapses_count }}</td>
                                <td class="text-nowrap">
                                    <a class="btn btn-uft btn-sm" href="{{ route('programas.pensum', $program->id) }}" title="Pensum"><i class="fas fa-book-open"></i></a>
                                    @if ($canManage)
                                        <a class="btn btn-uft btn-sm" href="{{ route('programas.edit', $program->id) }}" title="Editar"><i class="fas fa-edit"></i></a>
                                        @if ($program->users_count === 0 && $program->lapses_count === 0)
                                            <form action="{{ route('programas.destroy', $program->id) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('¿Eliminar «{{ $program->name }}»?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-uft btn-sm" title="Eliminar"><i class="fas fa-trash"></i></button>
                                            </form>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No hay programas en tu alcance.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($canManage)
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-uft">Nuevo programa</h6></div>
            <div class="card-body">
                @if (empty($faculties))
                    <p class="mb-0">Primero crea una <a href="{{ route('facultades.index') }}">facultad</a>: cada programa pertenece a una.</p>
                @else
                    {!! Form::open(['route' => 'programas.store', 'method' => 'POST']) !!}
                        <div class="form-row">
                            <div class="form-group col-md-5">
                                {!! Form::label('name', 'Nombre') !!}
                                {!! Form::text('name', old('name'), ['class' => 'form-control', 'required', 'maxlength' => 255]) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('faculty_id', 'Facultad') !!}
                                {!! Form::select('faculty_id', $faculties, old('faculty_id'), ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione']) !!}
                            </div>
                            <div class="form-group col-md-3">
                                {!! Form::label('min_passing_grade', 'Nota mínima (0–100)') !!}
                                {!! Form::number('min_passing_grade', old('min_passing_grade', \App\Modules\Academic\Domain\PassingGrade::DEFAULT), ['class' => 'form-control', 'required', 'min' => 0, 'max' => 100, 'step' => '0.01']) !!}
                            </div>
                            <div class="form-group col-12">
                                {!! Form::label('description', 'Descripción') !!}
                                {!! Form::textarea('description', old('description'), ['class' => 'form-control', 'rows' => 2]) !!}
                            </div>
                        </div>
                        <button type="submit" class="btn btn-uft"><i class="fas fa-save mr-2"></i>Crear programa</button>
                    {!! Form::close() !!}
                @endif
            </div>
        </div>
    @endif
@endsection
