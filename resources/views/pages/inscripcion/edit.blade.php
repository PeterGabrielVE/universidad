<!-- Modal -->

<div class="modal fade" id="modal_semestre" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">Actualizar Curso</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-row">
					<div class="col-md-12">
						<form id="formCarga">
							@csrf
							
							<div class="form-row">
								<div class="form-group col-6 m-0">
									{!! Form::label('Asignatura', 'Asignatura', ['class'=>'col-form-label s-12']) !!}
									{!! Form::text('course', null, ['class'=>'form-control r-0 light s-12', 'id'=>'course','readonly'=>'true']) !!}
									{!! Form::hidden('id', null, ['class'=>'form-control r-0 light s-12', 'id'=>'id']) !!}
									

									<span class="description"></span>
								</div>
								
								<div class="form-group col-6 m-0">
									{!! Form::label('nota', 'Nota:', ['class'=>'col-form-label s-12']) !!}
									{!! Form::text('note',null, ['class'=>'form-control r-0 light s-12', 'id'=>'note']) !!}
									<span class="note"></span>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<br>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" class="btn btn-uft" onclick="actualizarLapso()"><i class="icon-save mr-2"></i>Guardar</button>
			</div>
		</div>
	</div>
</div>
<script>
	function actualizarLapso(){
		 var url ="{{url('updateLapso')}}";
		 var formData = $('#formCarga').serialize();
          $.ajax({
            type : 'POST',
            url  : url,
            data : formData,
            success:function(data){
             	$("#modal_semestre").modal('hide');
            	location.reload();
            }
          });
	}
</script>