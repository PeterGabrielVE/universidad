@extends('layouts.panel')

{{-- Secciones de un lapso: Lapso → Asignatura → Sección → Docente titular (§17). --}}
@section('title')
    Secciones · {{ $lapse->name }}
@endsection

@section('actions')
    <a href="{{ route('lapso.index') }}" class="btn btn-uft"><i class="fas fa-arrow-left pr-2"></i>Lapsos</a>
@endsection

@section('panel')
    <p class="text-muted">
        {{ $lapse->program?->name ?? 'Sin programa' }} · Estado: {{ $lapse->status->label() }}
    </p>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Asignatura</th>
                            <th>Sección</th>
                            <th>Docente titular</th>
                            <th class="text-right">Activos</th>
                            <th class="text-right">Preinscritos</th>
                            <th class="text-right">Sin nota</th>
                            <th>Acta</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sections as $section)
                            <tr>
                                <td>{{ $section->course?->cod }} — {{ $section->course?->name }}</td>
                                <td>{{ $section->code }}</td>
                                <td>{{ $section->teacher ? trim($section->teacher->name . ' ' . $section->teacher->last_name) : 'Sin docente' }}</td>
                                <td class="text-right">{{ $section->active_count }}</td>
                                <td class="text-right">{{ $section->pending_count }}</td>
                                <td class="text-right">{{ $section->ungraded_count }}</td>
                                <td>
                                    {{ $section->status->label() }}
                                    @if ($section->closed_forcibly)
                                        <span class="badge badge-warning">Cierre forzoso</span>
                                    @endif
                                </td>
                                <td class="text-nowrap">
                                    <a class="btn btn-uft btn-sm" href="{{ route('secciones.show', $section->id) }}">Ver</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">Este lapso aún no tiene secciones.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if ($canManage && $lapse->status !== \App\Modules\Academic\Domain\LapseStatus::Cerrado)
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-uft">Abrir sección</h6></div>
            <div class="card-body">
                @if (empty($courses))
                    <p class="mb-0">El programa no tiene asignaturas en su <a href="{{ route('programas.pensum', $lapse->program_id) }}">pensum</a>.</p>
                @elseif (empty($teachers))
                    <p class="mb-0">No hay usuarios activos con rol Docente.</p>
                @else
                    {!! Form::open(['route' => ['secciones.store', $lapse->id], 'method' => 'POST']) !!}
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                {!! Form::label('course_id', 'Asignatura del pensum') !!}
                                {!! Form::select('course_id', $courses, old('course_id'), ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione']) !!}
                            </div>
                            <div class="form-group col-md-2">
                                {!! Form::label('code', 'Sección') !!}
                                {!! Form::text('code', old('code', 'A'), ['class' => 'form-control', 'required', 'maxlength' => 20]) !!}
                            </div>
                            <div class="form-group col-md-4">
                                {!! Form::label('teacher_id', 'Docente titular') !!}
                                {!! Form::select('teacher_id', $teachers, old('teacher_id'), ['class' => 'form-control', 'required', 'placeholder' => 'Seleccione']) !!}
                            </div>
                        </div>
                        <button type="submit" class="btn btn-uft"><i class="fas fa-plus mr-2"></i>Abrir sección</button>
                    {!! Form::close() !!}
                @endif
            </div>
        </div>
    @endif
@endsection
