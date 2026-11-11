@extends('layouts.panel')

{{-- Sección: inscripción (Asistente/Coordinador), supervisión del acta y, con el acta
     cerrada, correcciones auditadas y cierre forzoso (Administrador). --}}
@php
    $lapseClosed = $section->lapse->status === \App\Modules\Academic\Domain\LapseStatus::Cerrado;
    $sheetOpen = $section->status === \App\Modules\Academic\Domain\GradeSheetStatus::Abierta;
@endphp

@section('title')
    {{ $section->course?->cod }} {{ $section->course?->name }} · Sección {{ $section->code }}
@endsection

@section('actions')
    <a href="{{ route('secciones.index', $section->lapse_id) }}" class="btn btn-uft"><i class="fas fa-arrow-left pr-2"></i>Secciones</a>
@endsection

@section('panel')
    <div class="card shadow mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Lapso</dt><dd class="col-sm-9">{{ $section->lapse->name }} ({{ $section->lapse->status->label() }})</dd>
                <dt class="col-sm-3">Programa</dt><dd class="col-sm-9">{{ $section->lapse->program?->name }}</dd>
                <dt class="col-sm-3">Docente titular</dt>
                <dd class="col-sm-9">
                    {{ $section->teacher ? trim($section->teacher->name . ' ' . $section->teacher->last_name) : 'Sin docente' }}
                    @if ($can['manage'] && $sheetOpen && ! $lapseClosed)
                        {!! Form::open(['route' => ['secciones.update', $section->id], 'method' => 'PUT', 'class' => 'form-inline mt-2']) !!}
                            {!! Form::select('teacher_id', $teachers, $section->teacher_id, ['class' => 'form-control form-control-sm mr-2', 'aria-label' => 'Docente titular']) !!}
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Cambiar docente</button>
                        {!! Form::close() !!}
                    @endif
                </dd>
                <dt class="col-sm-3">Acta</dt>
                <dd class="col-sm-9">
                    {{ $section->status->label() }}
                    @if (! $sheetOpen)
                        el {{ $section->closed_at?->format('d/m/Y H:i') }} por {{ $section->closer ? trim($section->closer->name . ' ' . $section->closer->last_name) : '—' }}
                        @if ($section->closed_forcibly)
                            <span class="badge badge-warning">Cierre administrativo forzoso</span>
                        @endif
                    @endif
                </dd>
            </dl>

            @if ($can['forceClose'] && $sheetOpen)
                {!! Form::open(['route' => ['secciones.force_close', $section->id], 'method' => 'POST', 'class' => 'mt-3',
                    'onsubmit' => "return confirm('El acta quedará cerrada bajo tu autoría y el docente ya no podrá editarla. ¿Continuar?')"]) !!}
                    {!! Form::hidden('confirm', '1') !!}
                    <button type="submit" class="btn btn-warning"><i class="fas fa-lock mr-2"></i>Cierre administrativo forzoso</button>
                {!! Form::close() !!}
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-uft">Estudiantes</h6></div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Estudiante</th>
                            <th>Cédula</th>
                            <th>Inscripción</th>
                            <th class="text-right">Nota</th>
                            <th>Resultado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($enrollments as $enrollment)
                            @php($user = $enrollment->estudiante?->user)
                            <tr>
                                <td>{{ $user?->last_name }}, {{ $user?->name }}</td>
                                <td>{{ $user?->identification_card }}</td>
                                <td>{{ $enrollment->status?->label() }}</td>
                                <td class="text-right">{{ $enrollment->note !== null ? number_format($enrollment->note, 2) : '—' }}</td>
                                <td>{{ $enrollment->result?->label() ?? '—' }}</td>
                                <td class="text-nowrap">
                                    @if ($can['enroll'] && ! $lapseClosed && $enrollment->status === \App\Modules\Academic\Domain\EnrollmentStatus::PreInscrito)
                                        {!! Form::open(['route' => ['inscripciones.confirm', $enrollment->id], 'method' => 'POST', 'class' => 'd-inline']) !!}
                                            <button type="submit" class="btn btn-sm btn-uft">Confirmar</button>
                                        {!! Form::close() !!}
                                    @endif
                                    @if ($can['override'] && ! $sheetOpen && in_array($enrollment->status, [\App\Modules\Academic\Domain\EnrollmentStatus::Activo, \App\Modules\Academic\Domain\EnrollmentStatus::Cerrado], true))
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-toggle="collapse" data-target="#correct-{{ $enrollment->id }}">Corregir</button>
                                    @endif
                                </td>
                            </tr>
                            @if ($can['override'] && ! $sheetOpen)
                                <tr class="collapse" id="correct-{{ $enrollment->id }}">
                                    <td colspan="6">
                                        {!! Form::open(['route' => ['inscripciones.correct', $enrollment->id], 'method' => 'POST']) !!}
                                            <div class="form-row align-items-end">
                                                <div class="form-group col-md-2 mb-0">
                                                    {!! Form::label('note', 'Nota nueva') !!}
                                                    {!! Form::number('note', null, ['class' => 'form-control form-control-sm', 'min' => 0, 'max' => 100, 'step' => '0.01', 'required']) !!}
                                                </div>
                                                <div class="form-group col-md-8 mb-0">
                                                    {!! Form::label('justification', 'Justificación (obligatoria)') !!}
                                                    {!! Form::text('justification', null, ['class' => 'form-control form-control-sm', 'required', 'maxlength' => 1000, 'placeholder' => 'Ej.: Resolución de apelación N° 123']) !!}
                                                </div>
                                                <div class="form-group col-md-2 mb-0">
                                                    <button type="submit" class="btn btn-sm btn-danger btn-block">Registrar</button>
                                                </div>
                                            </div>
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No hay estudiantes inscritos.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($can['enroll'] && ! $lapseClosed && ! empty($candidates))
                <hr>
                {!! Form::open(['route' => ['secciones.enroll', $section->id], 'method' => 'POST']) !!}
                    <div class="form-group">
                        {!! Form::label('student_ids', 'Inscribir estudiantes del programa (quedan preinscritos hasta confirmar)') !!}
                        {!! Form::select('student_ids[]', $candidates, null, ['class' => 'form-control', 'multiple', 'size' => min(count($candidates), 8), 'required', 'id' => 'student_ids']) !!}
                    </div>
                    <button type="submit" class="btn btn-uft"><i class="fas fa-user-plus mr-2"></i>Inscribir</button>
                {!! Form::close() !!}
            @endif
        </div>
    </div>

    @if ($audit->isNotEmpty())
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-uft">Auditoría de correcciones</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr><th>Fecha</th><th>Estudiante</th><th class="text-right">Nota anterior</th><th class="text-right">Nota nueva</th><th>Resultado</th><th>Justificación</th><th>Por</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($audit as $log)
                            <tr>
                                <td>{{ \Illuminate\Support\Carbon::parse($log->created_at)->format('d/m/Y H:i') }}</td>
                                <td>{{ $log->student_name }}</td>
                                <td class="text-right">{{ $log->old_note !== null ? number_format($log->old_note, 2) : '—' }}</td>
                                <td class="text-right">{{ number_format($log->new_note, 2) }}</td>
                                <td>{{ $log->old_result ?? '—' }} → {{ $log->new_result ?? '—' }}</td>
                                <td>{{ $log->justification }}</td>
                                <td>{{ $log->changed_by_name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
