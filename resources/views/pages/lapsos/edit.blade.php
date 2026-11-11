<!-- Modal: editar lapso. Pasar a "Cerrado" ejecuta el cierre (CloseLapse) y es irreversible. -->
{!! Form::open(['class'=>'formlDinamic','id'=>'formData']) !!}
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="editLapseLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="editLapseLabel"><i class="icon icon-documents3 text-blue s-18"></i> Editar lapso académico</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div id="edit_errors" class="alert alert-danger d-none" role="alert"></div>
				<div class="form-row">
					<div class="form-group col-md-4">
						{!! Form::label('start_lapse', 'Fecha de inicio', ['class'=>'col-form-label s-12']) !!}
						{!! Form::date('start_lapse', null, ['class'=>'form-control date r-0 light s-12', 'id'=>'edit_start_lapse']) !!}
					</div>
					<div class="form-group col-md-4">
						{!! Form::label('end_lapse', 'Fecha final', ['class'=>'col-form-label s-12']) !!}
						{!! Form::date('end_lapse', null, ['class'=>'form-control date r-0 light s-12', 'id'=>'edit_end_lapse']) !!}
					</div>
					<div class="form-group col-md-4">
						{!! Form::label('status', 'Estado', ['class'=>'col-form-label s-12']) !!}
						{!! Form::select('status', ['planificacion'=>'En planificación', 'abierto'=>'Abierto', 'cerrado'=>'Cerrado (definitivo)'], null, ['class'=>'form-control r-0 light s-12', 'id'=>'edit_status']) !!}
						{!! Form::hidden('id', null, ['id'=>'_id']) !!}
					</div>
				</div>
				<p class="small text-muted mb-0">
					Al cerrar, cada inscripción activa del lapso se aprueba o reprueba según la nota mínima del
					programa y el lapso deja de admitir cambios.
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				<button type="button" class="btn btn-uft" onclick="actualizarLapso()"><i class="fas fa-save mr-2"></i>Guardar</button>
			</div>
		</div>
	</div>
</div>
{!! Form::close() !!}
<script>
	function actualizarLapso(){
		var id = $('#_id').val();
		var closing = $('#edit_status').val() === 'cerrado' && $('#edit_status').data('original') !== 'cerrado';
		if (closing && !confirm('Cerrar el lapso es definitivo: las notas quedarán fijas. ¿Continuar?')) {
			return;
		}
		$('#edit_errors').addClass('d-none').empty();
		$.ajax({
			type : 'post',
			url  : "{{ url('updateLapso') }}/" + id,
			data : $('#formData').serialize(),
			headers : { 'Accept': 'application/json' },
			success : function () {
				$('#edit').modal('hide');
				location.reload();
			},
			error : function (xhr) {
				var body = xhr.responseJSON || {};
				var messages = body.errors ? Object.values(body.errors).flat() : [body.message || 'No se pudo guardar el lapso.'];
				$('#edit_errors').removeClass('d-none').html(messages.map(function (m) { return $('<div>').text(m).html(); }).join('<br>'));
			}
		});
	}
</script>
