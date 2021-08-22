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
                                        <h6 class="m-0 font-weight-bold text-uft">Registrar Datos</h6>
                                    </div>
                                    <div class="pull-right">
                                        <a href="{{ url()->previous() }}" class="btn btn-uft" role="button"><i class="fas fa-arrow-left pr-2"></i>Atras </a>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="row">
                                 <div class="col-lg-12">
                                    <div>
                                        {!! Form::open(['route'=>'estudiantes.store','method'=>'POST', 'class'=>'user']) !!}
                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    {!! Form::label('lbl_nombres', 'Nombres:', ['class'=>'col-form-label s-12']) !!}
                                                    <input type="text" class="form-control" id="name" name="first_name"
                                                        placeholder="Ingrese sus nombres">
                                                </div>
                                                <div class="col-sm-4">
                                                    {!! Form::label('lbl_apellidos', 'Apellidos:', ['class'=>'col-form-label s-12']) !!}
                                                    <input type="text" class="form-control" id="last_name" name="last_name"
                                                        placeholder="Ingrese sus apellidos">
                                                </div>
                                                <div class="col-sm-4">
                                                    {!! Form::label('lbl_cedula', 'Cédula:', ['class'=>'col-form-label s-12']) !!}
                                                    <input type="text" class="form-control" id="identification_card" name="identification_card"
                                                    placeholder="Ingrese su cédula">
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-sm-6 mb-3 mb-sm-0">
                                                    {!! Form::label('lbl_pais', 'País donde vive:', ['class'=>'col-form-label s-12']) !!}
                                                   {!! Form::select('country_id', $paises, null, ['class'=>'form-control', 'id'=>'country_id']) !!}
                                               </div>
                                                <div class="col-sm-2 mb-3 mb-sm-0">
                                                    {!! Form::label('lbl_cedula', 'Télefono:', ['class'=>'col-form-label s-12']) !!}
                                                    {!! Form::select('cod_phone', $prefijo,null, ['class'=>'form-control', 'id'=>'cod_phone']) !!}
                                                </div>
                                                <div class="col-sm-3 mb-3 mb-sm-0">
                                                    {!! Form::label('lbl_cedula', ' ', ['class'=>'col-form-label s-12']) !!}
                                                    <input type="text" class="form-control mt-3" id="phone" name="phone" placeholder="Télefono">
                                                </div>
                                            </div>

                                            <div class="form-group row">
                                                <div class="col-sm-4">
                                                    {!! Form::label('lbl_pais', 'Lapso:', ['class'=>'col-form-label s-12']) !!}
                                                    {!! Form::select('lapse_id', $lapso, null, ['class'=>'form-control', 'id'=>'lapse_id']) !!}

                                                    <input type="hidden" class="form-control form-control-user"
                                                        id="status"
                                                        name="status"
                                                         value="1">
                                                </div>
                                                <div class="col-sm-4">
                                                    {!! Form::label('lbl_Sede', 'Sede:', ['class'=>'col-form-label s-12']) !!}
                                                   {!! Form::select('sede_id', $sedes, null, ['class'=>'form-control', 'id'=>'sede_id']) !!}
                                               </div>
                                               <div class="col-sm-4">
                                                    {!! Form::label('lbl_Doctorado', 'Doctorado:', ['class'=>'col-form-label s-12']) !!}
                                                   {!! Form::select('doctorado_id', $doctorados, null, ['class'=>'form-control', 'id'=>'doctorado']) !!}
                                               </div>
                                           </div>
                                            <div class="form-group row m-2" id="gerencia" style="display:none;">
                                                @include('pages.estudiantes.documents')
                                            </div>
                                            <div class="form-group row m-2" id="ciencia" style="display:none;">
                                                @include('pages.estudiantes.all_documents')
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-3"></div>
                                                <div class="col-lg-3">

                                                    <button type="submit" class="btn btn-uft btn-block" style="width: 400px;"><i class="icon-save mr-2"></i>Guardar Datos</button>
                                                </div>
                                                <div class="col-lg-3"></div>
                                            </div>

                                       {!! Form::close() !!}

                                    </div>
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
    $(".file").fileinput({
			showCaption: false,
			showRemove: false,
			showUpload: false,
			showBrowse: false,
			browseOnZoneClick: true,
	});
    $("#doctorado").on("click change", function( event ) {
		let valor = $(this).val();

        if(valor == 1){
            $('#gerencia').show();
            $('#ciencia').hide();
        }else if(valor == 2){
            $('#ciencia').show();
            $('#gerencia').hide();
        }else{
            $('#ciencia').hide();
            $('#gerencia').hide();
        }
	});
</script>
@endsection
