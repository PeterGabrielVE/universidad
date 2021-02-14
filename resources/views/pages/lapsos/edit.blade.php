<!-- Modal -->
{!! Form::open(['class'=>'formlDinamic','id'=>'formData']) !!}
<div class="modal fade" id="edit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel"><i class="icon icon-documents3 text-blue s-18"></i> Editar Lapso Academico</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-row">
					<div class="col-md-12">
						<div class="form-row">
							<div class="form-group col-4 m-0">
								<i class="icon-file-text mr-2"></i>
								{!! Form::label('start_lapse', 'Fecha Inicio', ['class'=>'col-form-label s-12']) !!}
								{!! Form::date('start_lapse', null, ['class'=>'form-control date r-0 light s-12',  'id'=>'edit_start_lapse']) !!}
								<span class="start_lapse_span"></span>
							</div>
							<div class="form-group col-4 m-0">
								<i class="icon-file-text mr-2"></i>
								{!! Form::label('end_lapse', 'Fecha Final', ['class'=>'col-form-label s-12']) !!}
								{!! Form::date('end_lapse', null, ['class'=>'form-control date r-0 light s-12',  'id'=>'edit_end_lapse']) !!}
								<span class="end_lapse_span"></span>
								
							</div>
							<div class="form-group col-4 m-0">
								<i class="icon-file-text mr-2"></i>
								{!! Form::label('status', 'Estatus', ['class'=>'col-form-label s-12']) !!}
								<span class="status_span"></span>
								{!! Form::select('status',['0'=>'terminado','1'=>'presente'], null, ['class'=>'form-control r-0 light s-12',  'id'=>'edit_status']) !!}
								{!! Form::hidden('id', null, ['class'=>'form-control date r-0 light s-12',  'id'=>'_id']) !!}
							</div>
						</div>
						<div class="form-row">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="button" class="btn btn-uft" onclick="actualizarLapso()"><i class="fas fa-save mr-2"></i>Guardar Datos</button>
			</div>
		</div>
	</div>
</div>
{!! Form::close() !!}
<script>
	function actualizarLapso(){
		  var id = $('#_id').val();
		  var url ="{{url('updateLapso')}}/"+id;
		  var formData = $('#formData').serialize();
          $.ajax({
            type : 'post',
            url  : url,
            data : formData,
            success:function(data){
              $('#edit').modal('hide')
              location.reload();
            }
          });
	}
</script>