@extends('layouts.app')

@section('content')
<div id="wrapper">

        <!-- Sidebar -->
        @include('layouts.sidebar')
        <!-- End of Sidebar -->

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
                    <h1 class="h3 mb-2 text-gray-800">{{ Auth::user()->first_name ?? null }} {{ Auth::user()->last_name ?? null }}</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h6 class="m-0 font-weight-bold text-uft">Documentos de Gerencia Avanzada</h6>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{{route('estudiantes.index')}}" class="btn btn-uft" role="button"><i class="fas fa-arrow-left pr-2"></i>Atras </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="pull-left">

                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Publ. 1 - Extenso</th>
                                                <th>Publ. 1 - Constancia de Aceptación</th>
                                                <th>Publ. 2 - Extenso</th>
                                                <th>Publ. 2 - Constancia de Aceptación</th>
                                                <th>Ponencia - Extenso</th>
                                                <th>Ponencia - Carta de Aceptación</th>
                                                <th>Ponencia  - Presentación</th>
                                                <th>Ponencia  - Certificado</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td>@if(isset($post1->extenso))
                                                        @if(isset($post1->extenso_note))
                                                            @if($post1->extenso_note == 1)
                                                                <b>Nota:Aprobado</b>
                                                            @else
                                                                <b>Nota: No Aprobado</b>
                                                            @endif
                                                        @endif
                                                    <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$post1->extenso.'')}}">
                                                    <i class="fas fa-download text-uft"></i></a>
                                                    <a class="btn btn-default btn-sm" title="Actualizar" href="{{url('edit/documentPost1/'.$estudiante->id.'/1')}}">
                                                        <i class="fas fa-edit text-uft"></i></a>
                                                    @else
                                                    <a class="btn btn-default btn-sm" title="Subir" href="{{ route('estudiante.documentos',[$estudiante->id,1])}}">
                                                    <i class="fas fa-upload text-uft"></i></a>
                                                    @endif
                                                </td>
                                                <td>@if(isset($post1->carta_aceptacion))
                                                    @if(isset($post1->carta_aceptacion_note))
                                                        @if($post1->carta_aceptacion_note == 1)
                                                            <b>Nota:Aprobado</b>
                                                        @else
                                                            <b>Nota: No Aprobado</b>
                                                        @endif
                                                    @endif
                                                    <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$post1->carta_aceptacion.'')}}">
                                                    <i class="fas fa-download text-uft"></i></a>
                                                    <a class="btn btn-default btn-sm" title="Actualizar" href="{{url('edit/documentPost1/'.$estudiante->id.'/1')}}">
                                                        <i class="fas fa-edit text-uft"></i></a>
                                                    @else
                                                    <a class="btn btn-default btn-sm" title="Subir" href="{{ route('estudiante.documentos',[$estudiante->id,'1'])}}">
                                                    <i class="fas fa-upload text-uft"></i></a>
                                                    @endif
                                                </td>
                                                <td>@if(isset($post2->extenso))
                                                    @if(isset($post2->extenso_note))
                                                        @if($post2->extenso_note == 1)
                                                            <b>Nota:Aprobado</b>
                                                        @else
                                                            <b>Nota: No Aprobado</b>
                                                        @endif
                                                    @endif
                                                    <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$post2->extenso.'')}}">
                                                    <i class="fas fa-download text-uft"></i></a>
                                                    <a class="btn btn-default btn-sm" title="Actualizar" href="{{url('edit/documentPost2/'.$estudiante->id.'/1')}}">
                                                        <i class="fas fa-edit text-uft"></i></a>
                                                    @else
                                                    <a class="btn btn-default btn-sm" title="Subir" href="{{ route('estudiante.documento.post2',[$estudiante->id,1])}}">
                                                    <i class="fas fa-upload text-uft"></i></a>
                                                    @endif
                                                </td>
                                                <td>@if(isset($post2->carta_aceptacion))
                                                        @if(isset($post2->carta_aceptacion_note))
                                                            @if($post2->carta_aceptacion_note == 1)
                                                                <b>Nota:Aprobado</b>
                                                            @else
                                                                <b>Nota: No Aprobado</b>
                                                            @endif
                                                        @endif
                                                    <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$post2->carta_aceptacion.'')}}">
                                                    <i class="fas fa-download text-uft"></i></a>
                                                    <a class="btn btn-default btn-sm" title="Actualizar" href="{{url('edit/documentPost2/'.$estudiante->id.'/1')}}">
                                                        <i class="fas fa-edit text-uft"></i></a>
                                                    @else
                                                    <a class="btn btn-default btn-sm" title="Subir" href="{{ route('estudiante.documento.post2',[$estudiante->id,1])}}">
                                                    <i class="fas fa-upload text-uft"></i></a>
                                                    @endif
                                                </td>

                                                <td>@if(isset($pre->extenso))
                                                    @if(isset($pre->extenso_note))
                                                        @if($pre->extenso_note == 1)
                                                            <b>Nota:Aprobado</b>
                                                        @else
                                                            <b>Nota: No Aprobado</b>
                                                        @endif
                                                    @endif
                                                    <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$pre->extenso.'')}}">
                                                    <i class="fas fa-download text-uft"></i></a>
                                                    <a class="btn btn-default btn-sm" title="Actualizar" href="{{url('edit/documentPres/'.$estudiante->id.'/1')}}">
                                                        <i class="fas fa-edit text-uft"></i></a>
                                                    @else
                                                    <a class="btn btn-default btn-sm" title="Subir" href="{{ route('estudiante.presentation',[$estudiante->id,1])}}">
                                                    <i class="fas fa-upload text-uft"></i></a>
                                                    @endif
                                                </td>
                                                <td>@if(isset($pre->carta_aceptacion))
                                                        @if(isset($pre->carta_aceptacion_note))
                                                            @if($pre->carta_aceptacion_note == 1)
                                                                <b>Nota:Aprobado</b>
                                                            @else
                                                                <b>Nota: No Aprobado</b>
                                                            @endif
                                                        @endif
                                                    <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$pre->carta_aceptacion.'')}}">
                                                    <i class="fas fa-download text-uft"></i></a>
                                                    <a class="btn btn-default btn-sm" title="Actualizar" href="{{url('edit/documentPres/'.$estudiante->id.'/1')}}">
                                                        <i class="fas fa-edit text-uft"></i></a>
                                                    @else
                                                    <a class="btn btn-default btn-sm" title="Subir" href="{{ route('estudiante.presentation',[$estudiante->id,1])}}">
                                                    <i class="fas fa-upload text-uft"></i></a>
                                                    @endif
                                                </td>
                                                <td>@if(isset($pre->poster))
                                                    @if(isset($pre->poster_note))
                                                            @if($pre->poster_note == 1)
                                                                <b>Nota:Aprobado</b>
                                                            @else
                                                                <b>Nota: No Aprobado</b>
                                                            @endif
                                                     @endif
                                                    <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$pre->poster.'')}}">
                                                    <i class="fas fa-download text-uft"></i></a>
                                                    <a class="btn btn-default btn-sm" title="Actualizar" href="{{url('edit/documentPres/'.$estudiante->id.'/1')}}">
                                                        <i class="fas fa-edit text-uft"></i></a>
                                                    @else
                                                    <a class="btn btn-default btn-sm" title="Subir" href="{{ route('estudiante.presentation',[$estudiante->id,1])}}">
                                                    <i class="fas fa-upload text-uft"></i></a>
                                                    @endif
                                                </td>
                                                <td>@if(isset($pre->certificado))
                                                    @if(isset($pre->certificado_note))
                                                        @if($pre->certificado_note == 1)
                                                            <b>Nota:Aprobado</b>
                                                        @else
                                                            <b>Nota: No Aprobado</b>
                                                        @endif
                                                    @endif
                                                    <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$pre->certificado.'')}}">
                                                    <i class="fas fa-download text-uft"></i></a>
                                                    <a class="btn btn-default btn-sm" title="Actualizar" href="{{url('edit/documentPres/'.$estudiante->id.'/1')}}">
                                                        <i class="fas fa-edit text-uft"></i></a>
                                                    @else
                                                    <a class="btn btn-default btn-sm" title="Subir" href="{{ route('estudiante.presentation',[$estudiante->id,1])}}">
                                                    <i class="fas fa-upload text-uft"></i></a>
                                                    @endif
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>

                                </div>
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
@section('js')
<script src={{asset('bootstrap-fileinput/js/fileinput.js')}}></script>
<script src={{asset('bootstrap-fileinput/js/plugins/piexif.js')}}></script>
<script src={{asset('bootstrap-fileinput/js/plugins/sortable.js')}}></script>
<script src={{asset('bootstrap-fileinput/js/locales/es.js')}}></script>
<script src={{asset('bootstrap-fileinput/themes/gly/theme.js')}}></script>
<script>
	$('#dataTable').dataTable({
        "bPaginate": false, //Ocultar paginación
        "bFilter": false,
        "bInfo": false
    })
</script>
@endsection
