@extends('layouts.panel')

@section('title', 'Mis secciones')

@section('panel')
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>Lapso</th>
                            <th>Programa</th>
                            <th>Asignatura</th>
                            <th>Sección</th>
                            <th class="text-right">Activos</th>
                            <th class="text-right">Sin nota</th>
                            <th>Acta</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sections as $section)
                            <tr>
                                <td>{{ $section->lapse?->name }}</td>
                                <td>{{ $section->lapse?->program?->name }}</td>
                                <td>{{ $section->course?->cod }} — {{ $section->course?->name }}</td>
                                <td>{{ $section->code }}</td>
                                <td class="text-right">{{ $section->active_count }}</td>
                                <td class="text-right">{{ $section->ungraded_count }}</td>
                                <td>{{ $section->status->label() }}</td>
                                <td><a class="btn btn-uft btn-sm" href="{{ route('docente.acta', $section->id) }}">Acta</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No tienes secciones asignadas.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
