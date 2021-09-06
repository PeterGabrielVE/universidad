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

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h6 class="m-0 font-weight-bold text-uft">Documentos Estudiante: {{ $estudiante->user->first_name ?? null }} {{ $estudiante->user->last_name ?? null }}</h6>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{{ url()->previous() }}" class="btn btn-uft" role="button"><i class="fas fa-arrow-left pr-2"></i>Atras </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 mb-4">
                                    <div class="pull-left">
                                          <h6>Gerencia Avanzada</h6>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <form action="{{ route('calificacion.store',$estudiante->id) }}"  method="POST">
                                        {{ csrf_field() }}
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th  style="width:35%">Documento</th>
                                                <th>Calificación</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr>
                                                <td>@if(isset($post1_gerencia->extenso))
                                                    <div class="row p-2">
                                                        <p>Publicación 1: Extenso</p>
                                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$post1_gerencia->extenso.'')}}">
                                                        <i class="fas fa-download text-uft"></i></a>
                                                    </div>
                                                    @else
                                                        <p>Publicación 1: No posee</p>
                                                    @endif
                                                </td>
                                                <td>@if(isset($post1_gerencia->extenso))
                                                    {!! Form::select('post1_extenso_note', $calificacion,$post1_gerencia->extenso_note ?? '', ['class'=>'form-control', 'id'=>'post1_extenso_note']) !!}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@if(isset($post1_gerencia->carta_aceptacion))
                                                    <div class="row p-2">
                                                        <p>Publicación 1: Carta de Aceptación</p>
                                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$post1_gerencia->carta_aceptacion.'')}}">
                                                        <i class="fas fa-download text-uft"></i></a>
                                                    </div>
                                                    @else
                                                        <p>Publicación 1: No posee</p>
                                                    @endif
                                                </td>
                                                <td>@if(isset($post1_gerencia->carta_aceptacion))
                                                    {!! Form::select('post1_carta_aceptacion_note', $calificacion,$post1_gerencia->carta_aceptacion_note ?? '', ['class'=>'form-control', 'id'=>'post1_extenso_note']) !!}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@if(isset($post2_gerencia->extenso))
                                                    <div class="row p-2">
                                                        <p>Publicación 2: Extenso</p>
                                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$post2_gerencia->extenso.'')}}">
                                                        <i class="fas fa-download text-uft"></i></a>
                                                    </div>
                                                    @else
                                                        <p>Publicación 2: Extenso - No posee</p>
                                                    @endif
                                                </td>
                                                <td>@if(isset($post2_gerencia->extenso))
                                                    {!! Form::select('post2_extenso_note', $calificacion,$post2_gerencia->extenso_note ?? '', ['class'=>'form-control', 'id'=>'post2_extenso_note']) !!}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@if(isset($post2_gerencia->carta_aceptacion))
                                                    <div class="row p-2">
                                                        <p>Publicación 2: Carta de Aceptación</p>
                                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$post2_gerencia->carta_aceptacion.'')}}">
                                                        <i class="fas fa-download text-uft"></i></a>
                                                    </div>
                                                    @else
                                                        <p>Publicación 2: Carta de Aceptación - No posee</p>
                                                    @endif
                                                </td>
                                                <td>@if(isset($post2_gerencia->carta_aceptacion))
                                                    {!! Form::select('post2_carta_aceptacion_note', $calificacion,$post2_gerencia->extenso_note ?? '', ['class'=>'form-control', 'id'=>'post2_extenso_note']) !!}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@if(isset($pre_gerencia->extenso))
                                                    <div class="row p-2">
                                                        <p>Ponencia: Extenso</p>
                                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$pre_gerencia->extenso.'')}}">
                                                        <i class="fas fa-download text-uft"></i></a>
                                                    </div>
                                                    @else
                                                        <p>Ponencia: Extenso - No posee</p>
                                                    @endif
                                                </td>
                                                <td>@if(isset($pre_gerencia->extenso))
                                                    {!! Form::select('pre_extenso_note', $calificacion,$pre_gerencia->extenso_note ?? '', ['class'=>'form-control', 'id'=>'pre_extenso_note']) !!}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@if(isset($pre_gerencia->carta_aceptacion))
                                                    <div class="row p-2">
                                                        <p>Ponencia: Carta de Aceptación</p>
                                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$pre_gerencia->carta_aceptacion.'')}}">
                                                        <i class="fas fa-download text-uft"></i></a>
                                                    </div>
                                                    @else
                                                        <p>Ponencia: Carta de Aceptación - No posee</p>
                                                    @endif
                                                </td>
                                                <td>@if(isset($pre_gerencia->carta_aceptacion))
                                                    {!! Form::select('pre_carta_aceptacion_note', $calificacion,$pre_gerencia->carta_aceptacion_note ?? '', ['class'=>'form-control', 'id'=>'pre_carta_aceptacion_note']) !!}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@if(isset($pre_gerencia->poster))
                                                    <div class="row p-2">
                                                        <p>Ponencia: Poster</p>
                                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$pre_gerencia->poster.'')}}">
                                                        <i class="fas fa-download text-uft"></i></a>
                                                    </div>
                                                    @else
                                                        <p>Ponencia: Poster - No posee</p>
                                                    @endif
                                                </td>
                                                <td>@if(isset($pre_gerencia->poster))
                                                    {!! Form::select('pre_poster_note', $calificacion,$pre_ciencia->poster_gerencia_note ?? '', ['class'=>'form-control', 'id'=>'pre_poster_note']) !!}
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>@if(isset($pre_gerencia->certificado))
                                                    <div class="row p-2">
                                                        <p>Ponencia: Certificado</p>
                                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$pre_gerencia->certificado.'')}}">
                                                        <i class="fas fa-download text-uft"></i></a>
                                                    </div>
                                                    @else
                                                        <p>Ponencia: Certificado - No posee</p>
                                                    @endif
                                                </td>
                                                <td>@if(isset($pre_ciencia->certificado))
                                                    {!! Form::select('pre_certificado_note', $calificacion,$pre_ciencia->certificado_note ?? '', ['class'=>'form-control', 'id'=>'pre_certificado_note']) !!}
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div class="row">
                                        <div class="col-4"></div>
                                        <div class="col-4 text-center">
                                            <button class="btn btn-uft btn-block" type="submit" style="width:200px;">Calificar</button>
                                        </div>
                                        <div class="col-4"></div>
                                    </div>
                                </form>
                                </div>

                <!-- /.container-fluid -->

            </div>
            <div class="row">
                <div class="card-header py-3">
                    <div class="row">
                        <div class="col-lg-12 margin-tb">
                            <div class="pull-left">
                                <h6 class="m-0 font-weight-bold text-uft">Documentos Ciencias de la Educación</h6>
                            </div>

                        </div>
                    </div>

                </div>
                <div class="table-responsive">
                    <form action="{{ route('calificacion.store',$estudiante->id) }}"  method="POST">
                        {{ csrf_field() }}
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th  style="width:35%">Documento</th>
                                <th>Calificación</th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr>
                                <td>@if(isset($post1_ciencia->extenso))
                                    <div class="row p-2">
                                        <p>Publicación 1: Extenso</p>
                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$post1_ciencia->extenso.'')}}">
                                        <i class="fas fa-download text-uft"></i></a>
                                    </div>
                                    @else
                                        <p>Publicación 1: No posee</p>
                                    @endif
                                </td>
                                <td>@if(isset($post1_ciencia->extenso))
                                    {!! Form::select('post1_extenso_note', $calificacion,$post1_ciencia->extenso_note ?? '', ['class'=>'form-control', 'id'=>'post1_extenso_note']) !!}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>@if(isset($post1_ciencia->carta_aceptacion))
                                    <div class="row p-2">
                                        <p>Publicación 1: Carta de Aceptación</p>
                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$post1_ciencia->carta_aceptacion.'')}}">
                                        <i class="fas fa-download text-uft"></i></a>
                                    </div>
                                    @else
                                        <p>Publicación 1: No posee</p>
                                    @endif
                                </td>
                                <td>@if(isset($post1_ciencia->carta_aceptacion))
                                    {!! Form::select('post1_carta_aceptacion_note', $calificacion,$post1_ciencia->carta_aceptacion_note ?? '', ['class'=>'form-control', 'id'=>'post1_extenso_note']) !!}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>@if(isset($post2_ciencia->extenso))
                                    <div class="row p-2">
                                        <p>Publicación 2: Extenso</p>
                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$post2_ciencia->extenso.'')}}">
                                        <i class="fas fa-download text-uft"></i></a>
                                    </div>
                                    @else
                                        <p>Publicación 2: Extenso - No posee</p>
                                    @endif
                                </td>
                                <td>@if(isset($post2_ciencia->extenso))
                                    {!! Form::select('post2_extenso_note', $calificacion,$post2_ciencia->extenso_note ?? '', ['class'=>'form-control', 'id'=>'post2_extenso_note']) !!}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>@if(isset($post2_ciencia->carta_aceptacion))
                                    <div class="row p-2">
                                        <p>Publicación 2: Carta de Aceptación</p>
                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$post2_ciencia->carta_aceptacion.'')}}">
                                        <i class="fas fa-download text-uft"></i></a>
                                    </div>
                                    @else
                                        <p>Publicación 2: Carta de Aceptación - No posee</p>
                                    @endif
                                </td>
                                <td>@if(isset($post2_ciencia->carta_aceptacion))
                                    {!! Form::select('post2_carta_aceptacion_note', $calificacion,$post2_ciencia->extenso_note ?? '', ['class'=>'form-control', 'id'=>'post2_extenso_note']) !!}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>@if(isset($pre_ciencia->extenso))
                                    <div class="row p-2">
                                        <p>Ponencia: Extenso</p>
                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$pre_ciencia->extenso.'')}}">
                                        <i class="fas fa-download text-uft"></i></a>
                                    </div>
                                    @else
                                        <p>Ponencia: Extenso - No posee</p>
                                    @endif
                                </td>
                                <td>@if(isset($pre_ciencia->extenso))
                                    {!! Form::select('pre_extenso_note', $calificacion,$post2_ciencia->extenso_note ?? '', ['class'=>'form-control', 'id'=>'pre_extenso_note']) !!}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>@if(isset($pre_ciencia->carta_aceptacion))
                                    <div class="row p-2">
                                        <p>Ponencia: Carta de Aceptación</p>
                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$pre_ciencia->carta_aceptacion.'')}}">
                                        <i class="fas fa-download text-uft"></i></a>
                                    </div>
                                    @else
                                        <p>Ponencia: Carta de Aceptación - No posee</p>
                                    @endif
                                </td>
                                <td>@if(isset($pre_ciencia->carta_aceptacion))
                                    {!! Form::select('pre_carta_aceptacion_note', $calificacion,$pre_ciencia->carta_aceptacion_note ?? '', ['class'=>'form-control', 'id'=>'pre_carta_aceptacion_note']) !!}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>@if(isset($pre_ciencia->poster))
                                    <div class="row p-2">
                                        <p>Ponencia: Poster</p>
                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$pre_ciencia->poster.'')}}">
                                        <i class="fas fa-download text-uft"></i></a>
                                    </div>
                                    @else
                                        <p>Ponencia: Poster - No posee</p>
                                    @endif
                                </td>
                                <td>@if(isset($pre_ciencia->poster))
                                    {!! Form::select('pre_poster_note', $calificacion,$pre_ciencia->poster_note ?? '', ['class'=>'form-control', 'id'=>'pre_poster_note']) !!}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td>@if(isset($pre_ciencia->certificado))
                                    <div class="row p-2">
                                        <p>Ponencia: Certificado</p>
                                        <a class="btn btn-default btn-sm" title="Descargar" href="{{url('download/document/'.$pre_ciencia->certificado.'')}}">
                                        <i class="fas fa-download text-uft"></i></a>
                                    </div>
                                    @else
                                        <p>Ponencia: Certificado - No posee</p>
                                    @endif
                                </td>
                                <td>@if(isset($pre_ciencia->certificado))
                                    {!! Form::select('pre_certificado_note', $calificacion,$pre_ciencia->certificado_note ?? '', ['class'=>'form-control', 'id'=>'pre_certificado_note']) !!}
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-4"></div>
                        <div class="col-4 text-center">
                            <button class="btn btn-uft btn-block" type="submit" style="width:200px;">Calificar</button>
                        </div>
                        <div class="col-4"></div>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>

</div>

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
