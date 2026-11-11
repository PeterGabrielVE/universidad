@extends('layouts.panel')

{{-- Kardex / récord académico (#20): resultado fijado al cerrar cada lapso (§8). --}}
@section('title')
    Kardex · {{ $student->user?->name }} {{ $student->user?->last_name }}
@endsection

@section('panel')
    <p class="text-muted">
        Cédula {{ $student->user?->identification_card }} ·
        {{ $student->user?->programs->pluck('name')->join(', ') ?: 'Sin programa' }}
    </p>

    @forelse ($lapses as $group)
        @php($lapse = $group['lapse'])
        <div class="card shadow mb-3">
            <div class="card-header py-2">
                <h6 class="m-0 font-weight-bold text-uft">
                    {{ $lapse?->name }} · {{ $lapse?->program?->name }} · {{ $lapse?->status->label() }}
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr><th>Código</th><th>Asignatura</th><th class="text-right">UC</th><th>Sección</th><th>Inscripción</th><th class="text-right">Nota</th><th>Resultado</th></tr>
                    </thead>
                    <tbody>
                        @foreach ($group['enrollments'] as $enrollment)
                            <tr>
                                <td>{{ $enrollment->asignatura?->cod }}</td>
                                <td>{{ $enrollment->asignatura?->name }}</td>
                                <td class="text-right">{{ $enrollment->asignatura?->uc }}</td>
                                <td>{{ $enrollment->section?->code ?? '—' }}</td>
                                <td>{{ $enrollment->status?->label() }}</td>
                                <td class="text-right">{{ $enrollment->note !== null ? number_format($enrollment->note, 2) . '/100' : '—' }}</td>
                                <td>
                                    @if ($enrollment->result)
                                        <span class="badge {{ $enrollment->result === \App\Modules\Academic\Domain\EnrollmentResult::Aprobado ? 'badge-success' : 'badge-danger' }}">
                                            {{ $enrollment->result->label() }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="alert alert-info">Aún no hay inscripciones registradas.</div>
    @endforelse
@endsection
