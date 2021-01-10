<!-- Modal -->
{!! Form::open(['route'=>'lapso.store','method'=>'POST', 'class'=>'formlDinamic', 'id'=>'guardarRegistro']) !!}
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel"><i class="icon icon-documents3 text-blue s-18"></i> Crear Lapso Academico</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-row">
					<div class="col-md-12">
						<div class="form-row">
							<div class="form-group col-6 m-0">
								<i class="icon-file-text mr-2"></i>
								{!! Form::label('start_lapse', 'Fecha Inicio', ['class'=>'col-form-label s-12']) !!}
								{!! Form::date('start_lapse', null, ['class'=>'form-control date r-0 light s-12',  'id'=>'start_lapse']) !!}
								<span class="start_lapse_span"></span>
							</div>
							<div class="form-group col-6 m-0">
								<i class="icon-file-text mr-2"></i>
								{!! Form::label('end_lapse', 'Fecha Final', ['class'=>'col-form-label s-12']) !!}
								{!! Form::date('end_lapse', null, ['class'=>'form-control date r-0 light s-12',  'id'=>'end_lapse']) !!}
								<span class="end_lapse_span"></span>
								{!! Form::hidden('status', 'active', ['class'=>'form-control r-0 light s-12',  'id'=>'end_lapse']) !!}
							</div>
						</div>
						<div class="form-row">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" class="btn btn-uft"><i class="fas fa-save mr-2"></i>Guardar Datos</button>
			</div>
		</div>
	</div>
</div>
{!! Form::close() !!}