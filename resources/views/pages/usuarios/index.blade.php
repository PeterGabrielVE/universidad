@extends('layouts.app')

@section('content')
<div id="wrapper">

        <!-- Sidebar -->
        @include('layouts.sidebar')
        <!-- End of Sidebar -->
        @include('pages.usuarios.create')
        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                 @include('layouts.navbar')
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Usuarios</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h6 class="m-0 font-weight-bold text-uft">Listado Usuarios</h6>
                                    </div>
                                    <div class="pull-right">
                                        <a data-toggle="modal" data-target="#create" class="btn btn-uft" role="button"><i class="fas fa-plus pr-2"></i>Agregar Usuario</a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nombres y Apellidos</th>
                                            <th>Correo Electronico</th>
                                            <th>Perfil</th>

                                            <th style="width:16%">Opciones</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach ($usuarios as $user)
                                        <tr>

                                            <td>{{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}</td>

                                            <td>{{ $user->email ?? '' }}</td>

                                            <td>@if(isset($user->rol_id))
                                                {{ $user->rol->name ?? '' }}
                                                @endif
                                            </td>
                                            <td>
                                                <a class="btn btn-uft btn-sm" href="{{ route('user.edit',$user->id) }}"><i class="fas fa-edit"></i></a>
                                                <a class="btn btn-uft btn-sm" href="{{ route('user.delete',$user->id) }}"><i class="fas fa-trash"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                                {!! $usuarios->links() !!}
                            </div>
                        </div>
                    </div>

                </div>

                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->
@endsection
