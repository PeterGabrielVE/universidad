@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Rol</h2>
    <form action="{{ route('roles.update', $role->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="form-group">
            <label>Nombre del Rol</label>
            <input type="text" name="name" class="form-control" value="{{ $role->name }}" required>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Actualizar</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection
