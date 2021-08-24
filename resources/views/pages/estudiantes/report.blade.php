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
                    <h1 class="h3 mb-2 text-gray-800">Estudiantes</h1>

                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <div class="row">
                                <div class="col-lg-12 margin-tb">
                                    <div class="pull-left">
                                        <h6 class="m-0 font-weight-bold text-uft">Reportes</h6>
                                    </div>
                                    <div class="pull-right"></div>
                                </div>
                            </div>

                        </div>
                        <div class="card-body">
                            <div class="card-header py-3">
                               <div class="form-row">
                                  <div class="col-12"><h5>{{__('Reporte por sede')}}</h5></div>
                                    <div class="card-header py-3">
                                        <div class="btn-toolbar">
                                            <form class="form-inline" id="searchReportForm" method="POST" action="#">
                                                      {{ csrf_field() }}

                                                      <div class="form-group mr-2">
                                                          <label for="sede" class="mr-3">Sede</label>
                                                          <select class="form-control" name="budget_state" data-width="300px" id="sede" data-placeholder="{{__('Buscar Sede')}}..." data-allow-clear="1" required="true">
                                                              <option value="">Seleccione</option>
                                                             @foreach ($sedes as $s)
                                                                 <option value="{{ $s->id }}">{{ $s->name }}</option>
                                                             @endforeach
                                                         </select>
                                                      </div>
                                                      <div class="input-group">
                                                          <div class="col-12 dt-buttons btn-group flex-wrap">
                                                              <a class="btn btn-secondary buttons-csv buttons-html5"
                                                              onclick="downloadPdfSede()"
                                                             href="#" id="btn_compromiso_excel"> {{ __('Descargar') }}</a>
                                                          </div>
                                                      </div>

                                                  </form>
                                              </div>
                                        </div>

                                  </div>
                              </div>
                          </div>
                          <div class="card-body">
                            <div class="card-header py-3">
                               <div class="form-row">
                                  <div class="col-12"><h5>{{__('Reporte por Estudiante')}}</h5></div>
                                    <div class="card-header py-3">
                                        <div class="btn-toolbar">
                                            <form class="form-inline" id="searchReportForm" method="POST" action="#">
                                                      {{ csrf_field() }}
                                                      <div class="form-group mr-2">
                                                          <label for="budgetState" class="mr-3">Cédula</label>
                                                          <select class="form-control" name="budget_state" data-width="300px" id="budgetState" data-placeholder="{{__('Buscar Estudiante')}}..." data-allow-clear="1" required="true">
                                                              <option value="">Seleccione</option>
                                                             @foreach ($estudiantes as $ced)
                                                                 <option value="{{ $ced->id }}">{{ $ced->identification_card }}</option>
                                                             @endforeach
                                                         </select>
                                                      </div>
                                                      <div class="input-group">
                                                          <div class="col-12 dt-buttons btn-group flex-wrap">
                                                              <a class="btn btn-secondary buttons-csv buttons-html5"onclick="downloadCsvConfigCompromiso()" style="margin: 0px" href="#" id="btn_compromiso_csv"> {{ __('CSV') }}</a>
                                                              <a class="btn btn-secondary buttons-csv buttons-html5"
                                                              onclick="downloadExcelConfigCompromiso()"
                                                             href="#" id="btn_compromiso_excel"> {{ __('Excel') }}</a>

                                                          </div>
                                                      </div>
                                                      <div class="col-3">
                                                           <span class="text-danger m-0 p-0" id="span_since_compromiso" style="display: none;font-size: 12px;">{{__('La fecha no puede ser mayor que la final.')}}</span>
                                                           <span class="text-danger m-0 p-0" id="span_since_now_compromiso" style="display: none;font-size: 12px;">{{__('La fecha no puede ser mayor que la actual.')}}</span>
                                                      </div>
                                                      <div class="col-6">
                                                           <span class="text-danger m-0 p-0" id="span_until_compromiso" style="display: none;font-size: 12px;">{{__('La fecha no puede ser menor que la inicial.')}}</span>
                                                            <span class="text-danger m-0 p-0" id="span_until_now_compromiso" style="display: none;font-size: 12px;">{{__('La fecha no puede ser mayor que la actual.')}}</span>
                                                      </div>

                                                  </form>
                                              </div>
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
