<div class="form-group row">
    <div class="col-md-12">
        <h6 class="font-weight-bold text-uft">Ingrese todos los documentos</h6> <br/>
        <h6 class="font-weight-bold text-uft">Publicación 1</h6>
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('lbl_nombres', 'Extenso', ['class'=>'col-form-label s-12']) !!}
                    <input id="file" class="file ciencia_required" name="c_post1_extenso" type="file" size="15">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('lbl_nombres', 'Carta de Aceptación', ['class'=>'col-form-label s-12']) !!}
                    <input id="file2" class="file ciencia_required" name="c_post1_carta" type="file" size="15">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <h6 class="font-weight-bold text-uft">Publicación 2</h6>
        <div class="form-row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('lbl_nombres', 'Extenso', ['class'=>'col-form-label s-12']) !!}
                    <input id="file" class="file ciencia_required" name="c_post2_extenso" type="file" size="15">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('lbl_nombres', 'Carta de Aceptación', ['class'=>'col-form-label s-12']) !!}
                    <input id="file2" class="file ciencia_required" name="c_post2_carta" type="file" size="15">
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <h6 class="font-weight-bold text-uft">Ponencia</h6>
        <div class="form-row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('lbl_nombres', 'Extenso', ['class'=>'col-form-label s-12']) !!}
                    <input id="file1" class="file ciencia_required" name="c_extenso" type="file" size="15">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('lbl_nombres', 'Carta de Aceptación', ['class'=>'col-form-label s-12']) !!}
                    <input id="file2" class="file ciencia_required" name="c_carta_aceptacion" type="file" size="15">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('lbl_nombres', 'Extenso', ['class'=>'col-form-label s-12']) !!}
                    <input id="file3" class="file ciencia_required" name="c_poster" type="file" size="15">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('lbl_nombres', 'Carta de Aceptación', ['class'=>'col-form-label s-12']) !!}
                    <input id="file4" class="file ciencia_required" name="c_certificado" type="file" size="15">
                </div>
            </div>
        </div>
    </div>
</div>
