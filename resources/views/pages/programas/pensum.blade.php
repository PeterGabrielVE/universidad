@extends('layouts.panel')

{{-- $semesters: asignaturas del pensum agrupadas por semestre; $available: las que no están (solo si $canManage). --}}
@section('title')
    Pensum · {{ $program->name }}
@endsection

@section('actions')
    <a href="{{ route('programas.index') }}" class="btn btn-uft"><i class="fas fa-arrow-left pr-2"></i>Programas</a>
@endsection

@section('panel')
    <p class="text-muted">
        {{ $program->faculty?->name ?? 'Sin facultad' }} · Nota mínima {{ number_format($program->min_passing_grade, 2) }}
    </p>

    @forelse ($semesters as $semester => $courses)
        <div class="card shadow mb-3">
            <div class="card-header py-2"><h6 class="m-0 font-weight-bold text-uft">Semestre {{ $semester }}</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Asignatura</th>
                            <th class="text-right">UC</th>
                            <th>Prelación</th>
                            <th>Compartida</th>
                            @if ($canManage)<th></th>@endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $course)
                            <tr>
                                <td>{{ $course->cod }}</td>
                                <td>{{ $course->name }}</td>
                                <td class="text-right">{{ $course->uc }}</td>
                                <td>{{ $course->required ?: '—' }}</td>
                                <td>{{ $course->programs_count > 1 ? 'Sí (' . $course->programs_count . ' programas)' : 'No' }}</td>
                                @if ($canManage)
                                    <td class="text-right">
                                        <form action="{{ route('programas.pensum.detach', [$program->id, $course->id]) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link btn-sm text-danger p-0">Retirar</button>
                                        </form>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Este programa aún no tiene asignaturas en su pensum.</div>
    @endforelse

    @if ($canManage && ! empty($available))
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-uft">Añadir asignaturas</h6></div>
            <div class="card-body">
                {!! Form::open(['route' => ['programas.pensum.attach', $program->id], 'method' => 'POST']) !!}
                    <div class="form-group">
                        {!! Form::label('course_ids', 'Asignaturas (Ctrl/Cmd para elegir varias). Una asignatura transversal puede estar en varios programas.') !!}
                        {!! Form::select('course_ids[]', $available, null, ['class' => 'form-control', 'id' => 'course_ids', 'multiple', 'size' => 10, 'required']) !!}
                    </div>
                    <button type="submit" class="btn btn-uft"><i class="fas fa-plus mr-2"></i>Añadir al pensum</button>
                {!! Form::close() !!}
            </div>
        </div>
    @endif
@endsection
