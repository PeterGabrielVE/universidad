@extends('layouts.app')

@section('content')
    <div id="app">
        <div id="wrapper">
            @include('layouts.sidebar')
            <div id="content-wrapper" class="d-flex flex-column">
                <div id="content">
                    @include('layouts.navbar')
                    <div class="container-fluid">
                        <h1 class="h3 mb-2 text-gray-800">Crear Usuario</h1>

                        <div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <h6 class="m-0 font-weight-bold text-uft">Formulario de Registro de Usuario</h6>
                            </div>

                            <div class="card-body">
                                <!-- Aquí montamos Vue -->
                                <create-user></create-user>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

  
@endsection