<!-- Modal Create-->
{!! Form::open(['route'=>'user.store','method'=>'POST', 'class'=>'formlDinamic', 'id'=>'guardarRegistro']) !!}
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel"><i class="icon-person"></i> Agregar Nuevo Usuario</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-row">
					<div class="col-md-6">
						<div class="form-group m-0 has-feedback" id="campo_group">
							
							{!! Form::label('nombre', 'Nombre', ['class'=>'col-form-label s-12']) !!}
							{!! Form::text('name', null, ['class'=>'form-control r-0 light s-12', 'id'=>'name', 'onclick'=>'inputClear(this.id)']) !!}
							<span class="campo_span"></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group m-0" id="modulo_group">
							{!! Form::label('roles', 'Perfil', ['class'=>'col-form-label s-12']) !!}
							{!! Form::select('rol_id', $roles, null, ['class'=>'form-control r-0 light s-12', 'id'=>'rol_id', 'onclick'=>'inputClear(this.id)']) !!}
							<span class="status_span"></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group m-0 has-feedback" id="campo_group">
							
							{!! Form::label('metaListValue', 'Correo Electronico', ['class'=>'col-form-label s-12']) !!}
							{!! Form::email('email', null, ['class'=>'form-control r-0 light s-12', 'id'=>'email', 'onclick'=>'inputClear(this.id)']) !!}
							<span class="campo_span"></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group m-0 has-feedback" id="campo_group">
							
							{!! Form::label('Contraseña', 'Contraseña', ['class'=>'col-form-label s-12']) !!}
							{!! Form::text('password', null, ['class'=>'form-control r-0 light s-12', 'id'=>'password', 'onclick'=>'inputClear(this.id)']) !!}
							<span class="campo_span"></span>
						</div>
					</div>
					
					
				</div>
				
			</div>
			<br>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
				<button type="submit" class="btn btn-uft" onclick="cerrarModal()"><i class="icon-save mr-2"></i>Guardar Datos</button>
			</div>
		</div>
	</div>
</div>
{!! Form::close() !!}
<script>
	function cerrarModal(){
		$('#create').modal('hide');
	}
</script>