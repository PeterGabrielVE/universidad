<!-- Modal Create-->
{!! Form::open(['route'=>"user.store",'method'=>'POST', 'class'=>'user']) !!}
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="myModalLabel"><i class="icon-person"></i> Agregar Nuevo {{ $title ?? 'Usuario' }}</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="form-row">
                    @if($rol == 4)
                    {!! Form::hidden('rol_id', '4', ['class'=>'form-control r-0 light s-12', 'id'=>'rol_id']) !!}
                    @else
					<div class="col-md-4">
						<div class="form-group m-0 has-feedback" id="campo_group">
							{!! Form::label('nombre', 'Nombre', ['class'=>'col-form-label s-12']) !!}
							{!! Form::text('first_name', null, ['class'=>'form-control r-0 light s-12', 'id'=>'name']) !!}
							<span class="campo_span"></span>
						</div>
					</div>
                    <div class="col-md-4">
						<div class="form-group m-0 has-feedback" id="campo_group">
							{!! Form::label('apellidos', 'Apellidos', ['class'=>'col-form-label s-12']) !!}
							{!! Form::text('last_name', null, ['class'=>'form-control r-0 light s-12', 'id'=>'last_name']) !!}
							<span class="apellido_span"></span>
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group m-0" id="modulo_group">
							{!! Form::label('roles', 'Perfil', ['class'=>'col-form-label s-12']) !!}
							{!! Form::select('rol_id', $roles, $rol ?? null, ['class'=>'form-control r-0 light s-12', 'id'=>'rol_id']) !!}
							<span class="status_span"></span>
						</div>
					</div>
                    @endif
					<div class="col-md-6">
						<div class="form-group m-0 has-feedback" id="campo_group">
							{!! Form::label('metaListValue', 'Correo Electronico', ['class'=>'col-form-label s-12']) !!}
							{!! Form::email('email', null, ['class'=>'form-control r-0 light s-12', 'id'=>'email']) !!}
							<span class="campo_span"></span>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group m-0 has-feedback" id="campo_group">
							{!! Form::label('Contraseña', 'Contraseña', ['class'=>'col-form-label s-12']) !!}
							{!! Form::text('password', null, ['class'=>'form-control r-0 light s-12', 'id'=>'password']) !!}
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



