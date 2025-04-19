@extends('layouts.app')

@section('content')
    <div id="app"> <!-- Mueve el div#app aquí para envolver todo el contenido -->
        <div id="wrapper">
            @include('layouts.sidebar')
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    @include('layouts.navbar')
                    <div class="container-fluid">
                        <h1 class="h3 mb-2 text-gray-800">Roles</h1>

                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div class="row">
                                    <div class="col-lg-12 margin-tb">
                                        <div class="pull-left">
                                            <h6 class="m-0 font-weight-bold text-uft">Listado de Roles</h6>
                                        </div>
                                        <div class="pull-right">

                                            <button @click="showModal = true" class="btn btn-uft">
                                                <i class="fas fa-plus pr-2"></i>Crear Nuevo Rol
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                @if (session('success'))
                                    <div class="alert alert-success">{{ session('success') }}</div>
                                @endif
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Nombre</th>
                                                <th style="width:20%">Acciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($roles as $role)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $role->name }}</td>
                                                    <td>
                                                        <form action="{{ route('roles.destroy', $role->id) }}"
                                                            method="POST">
                                                            <a href="{{ route('roles.edit', $role->id) }}"
                                                                class="btn btn-uft btn-sm"><i class="fas fa-edit"></i></a>
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-uft btn-sm"
                                                                onclick="return confirm('¿Estás seguro de eliminar este rol?')"><i
                                                                    class="fas fa-trash"></i></button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Vue -->
        <create-role-modal :is-open="showModal" @close="showModal = false"></create-role-modal>
    </div> <!-- Cierre del div#app -->
@endsection
