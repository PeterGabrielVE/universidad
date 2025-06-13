@extends('layouts.app')

@section('content')
    <div id="app">
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
                                            <button @click="openModal()" class="btn btn-uft">
                                                <i class="fas fa-plus pr-2"></i>Crear Nuevo Rol
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div v-if="loading" class="text-center py-4">
                                    <div class="spinner-border text-uft" role="status"></div>

                                </div>

                                <template v-else>
                                    <div class="table-responsive">
                                        <table class="table table-bordered" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nombre</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(role, index) in roles" :key="role.id">
                                                    <td>@{{ index + 1 }}</td>
                                                    <td>@{{ role.name }}</td>
                                                    <td>
                                                        <button @click="editRole(role)" class="btn btn-uft btn-sm me-2">
                                                            <i class="fas fa-edit"></i> Editar
                                                        </button>
                                                        <button @click="confirmDelete(role.id)" class="btn btn-danger btn-sm">
                                                            <i class="fas fa-trash"></i> Eliminar
                                                        </button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div v-if="roles.length === 0" class="alert alert-info mt-3">
                                        No hay roles registrados. Crea tu primer rol.
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Component -->
        <create-role-modal
            :show="showModal"
            :role="currentRole"
            @close="closeModal"
            @saved="handleRoleSaved">
        </create-role-modal>
    </div>
@endsection

@push('scripts')
    <script>
        window.initialRoles = @json($roles);
        window.csrfToken = "{{ csrf_token() }}";
    </script>
@endpush
