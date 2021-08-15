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
                    <h1 class="h3 mb-2 text-gray-800">{{ Auth::user()->name ?? null }} {{ Auth::user()->last_name ?? null }}</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h6 class="m-0 font-weight-bold text-uft">Documentos Estudiante</h6>
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
                                        <h6 class="m-0 font-weight-bold text-uft">Publicación 2</h6>
                                    </div>
                                </div>
                                 <div class="col-lg-12">
                                    <div>
                                        {!! Form::open(['route'=>["estudiante.documentStore",$estudiante->id],'method'=>'POST', 'class'=>'user','files'=>true,'enctype'=>'multipart/form-data']) !!}
                                            <div class="form-group row">
                                                <div class="col-md-3 offset-md-1">
                                                    <div class="form-group">
                                                        {!! Form::label('lbl_nombres', 'Extenso', ['class'=>'col-form-label s-12']) !!}
                                                        <input id="file" class="file" name="post2_extenso" type="file" size="15" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 offset-md-1">
                                                    <div class="form-group">
                                                        {!! Form::label('lbl_nombres', 'Carta de Aceptación', ['class'=>'col-form-label s-12']) !!}
                                                        <input id="file2" class="file" name="post2_carta" type="file" size="15" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-lg-3"></div>
                                                <div class="col-lg-3">
                                                    <button type="submit" class="btn btn-uft btn-block" style="width: 400px;"><i class="icon-save mr-2"></i>Subir</button>
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

            <!-- Footer -->
            @include('layouts.footer')
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>
@endsection
@section('js')
<script src={{asset('bootstrap-fileinput/js/fileinput.js')}}></script>
<script src={{asset('bootstrap-fileinput/js/plugins/piexif.js')}}></script>
<script src={{asset('bootstrap-fileinput/js/plugins/sortable.js')}}></script>
<script src={{asset('bootstrap-fileinput/js/locales/es.js')}}></script>
<script src={{asset('bootstrap-fileinput/themes/gly/theme.js')}}></script>
<script>
		var title = 'Users';
		var colunms = [0,1,2,3,4];

		$(".file").fileinput({
			showCaption: false,
			showRemove: false,
			showUpload: false,
			showBrowse: false,
			browseOnZoneClick: true,
		});

    $(document).ready(function() {
        $('#account').addClass('active');
    });
</script>
@endsection
