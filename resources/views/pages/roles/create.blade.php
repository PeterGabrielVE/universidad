@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear Nuevo Rol</h2>
    <form action="{{ route('roles.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label>Nombre del Rol</label>
            <input type="text" name="name" class="form-control" required>
            <input type="hidden" name="guard_name" value="web">
        </div>
        <br>
        <button type="submit" class="btn btn-success">Guardar</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
