<!-- Modal: crear lapso. $programs solo incluye los programas del alcance del usuario. -->
{!! Form::open(['route'=>'lapso.store','method'=>'POST', 'class'=>'formlDinamic', 'id'=>'guardarRegistro']) !!}
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="createLapseLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="createLapseLabel"><i class="icon icon-documents3 text-blue s-18"></i> Crear lapso académico</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-row">
					<div class="form-group col-md-6">
						{!! Form::label('program_id', 'Programa', ['class'=>'col-form-label s-12']) !!}
						{!! Form::select('program_id', $programs, null, ['class'=>'form-control r-0 light s-12', 'id'=>'program_id', 'placeholder'=>'Seleccione', 'required']) !!}
					</div>
					<div class="form-group col-md-6">
						{!! Form::label('status', 'Estado inicial', ['class'=>'col-form-label s-12']) !!}
						{!! Form::select('status', ['planificacion'=>'En planificación', 'abierto'=>'Abierto'], 'planificacion', ['class'=>'form-control r-0 light s-12', 'id'=>'create_status']) !!}
					</div>
					<div class="form-group col-md-6">
						{!! Form::label('start_lapse', 'Fecha de inicio', ['class'=>'col-form-label s-12']) !!}
						{!! Form::date('start_lapse', null, ['class'=>'form-control date r-0 light s-12', 'id'=>'start_lapse', 'required']) !!}
					</div>
					<div class="form-group col-md-6">
						{!! Form::label('end_lapse', 'Fecha final', ['class'=>'col-form-label s-12']) !!}
						{!! Form::date('end_lapse', null, ['class'=>'form-control date r-0 light s-12', 'id'=>'end_lapse', 'required']) !!}
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" class="btn btn-uft"><i class="fas fa-save mr-2"></i>Guardar</button>
			</div>
		</div>
	</div>
</div>
{!! Form::close() !!}
