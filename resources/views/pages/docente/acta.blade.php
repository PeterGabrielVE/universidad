@extends('layouts.panel')

{{-- Acta del docente titular (§15): notas 0–100 de los estudiantes activos mientras el
     acta y el lapso estén abiertos; al cerrarla ya no puede modificarla. --}}
@php
    $editable = $section->status === \App\Modules\Academic\Domain\GradeSheetStatus::Abierta
        && $section->lapse->status === \App\Modules\Academic\Domain\LapseStatus::Abierto;
    $active = \App\Modules\Academic\Domain\EnrollmentStatus::Activo;
@endphp

@section('title')
    Acta · {{ $section->course?->cod }} {{ $section->course?->name }} · Sección {{ $section->code }}
@endsection

@section('actions')
    <a href="{{ route('docente.secciones') }}" class="btn btn-uft"><i class="fas fa-arrow-left pr-2"></i>Mis secciones</a>
@endsection

@section('panel')
    <p class="text-muted">
        {{ $section->lapse->name }} · {{ $section->lapse->program?->name }} ·
        Nota mínima {{ number_format($section->lapse->program?->min_passing_grade ?? \App\Modules\Academic\Domain\PassingGrade::DEFAULT, 2) }} ·
        Acta {{ mb_strtolower($section->status->label()) }}
        @if ($section->closed_forcibly)
            <span class="badge badge-warning">Cierre administrativo forzoso</span>
        @endif
    </p>

    @if (! $editable && $section->status === \App\Modules\Academic\Domain\GradeSheetStatus::Abierta)
        <div class="alert alert-info">El lapso no está abierto: las notas se registran cuando Control de Estudios lo abra.</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-body">
            {!! Form::open(['route' => ['docente.acta.record', $section->id], 'method' => 'POST']) !!}
                <table class="table table-sm">
                    <thead>
                        <tr><th>Estudiante</th><th>Cédula</th><th>Inscripción</th><th style="width: 10rem">Nota (0–100)</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollments as $enrollment)
                            @php($user = $enrollment->estudiante?->user)
                            <tr>
                                <td>{{ $user?->last_name }}, {{ $user?->name }}</td>
                                <td>{{ $user?->identification_card }}</td>
                                <td>{{ $enrollment->status?->label() }}</td>
                                <td>
                                    @if ($editable && $enrollment->status === $active)
                                        {!! Form::number("grades[{$enrollment->id}]", $enrollment->note, ['class' => 'form-control form-control-sm', 'min' => 0, 'max' => 100, 'step' => '0.01', 'aria-label' => 'Nota']) !!}
                                    @else
                                        {{ $enrollment->note !== null ? number_format($enrollment->note, 2) : '—' }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted">No hay estudiantes inscritos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($editable && $enrollments->contains(fn ($e) => $e->status === $active))
                    <button type="submit" class="btn btn-uft"><i class="fas fa-save mr-2"></i>Guardar notas</button>
                @endif
            {!! Form::close() !!}

            @if ($editable)
                {!! Form::open(['route' => ['docente.acta.close', $section->id], 'method' => 'POST', 'class' => 'mt-3',
                    'onsubmit' => "return confirm('Al cerrar el acta ya no podrás modificar las notas. ¿Continuar?')"]) !!}
                    {!! Form::hidden('confirm', '1') !!}
                    <button type="submit" class="btn btn-outline-danger"><i class="fas fa-lock mr-2"></i>Cerrar acta</button>
                {!! Form::close() !!}
            @endif
        </div>
    </div>
@endsection
