@extends('layouts.panel')

@section('title', 'Secciones e inscripción')

@section('panel')
    <div class="card shadow mb-4">
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr><th>Lapso</th><th>Programa</th><th>Estado</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse ($lapsos as $lapso)
                        <tr>
                            <td>{{ $lapso->name }}</td>
                            <td>{{ $lapso->program?->name ?? 'Sin programa' }}</td>
                            <td>{{ $lapso->status->label() }}</td>
                            <td><a class="btn btn-uft btn-sm" href="{{ route('secciones.index', $lapso->id) }}">Secciones</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-muted">No hay lapsos en tu alcance.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {!! $lapsos->links() !!}
        </div>
    </div>
@endsection
