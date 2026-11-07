<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="form-group mb-3">
            <label for="nombre" class="form-label fw-bold">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $dueño?->nombre) }}" id="nombre" placeholder="Nombre">
            @error('nombre')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="apellido" class="form-label fw-bold">{{ __('Apellido') }}</label>
            <input type="text" name="apellido" class="form-control @error('apellido') is-invalid @enderror" value="{{ old('apellido', $dueño?->apellido) }}" id="apellido" placeholder="Apellido">
            @error('apellido')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="telefono" class="form-label fw-bold">{{ __('Teléfono') }}</label>
            <input type="text" name="telefono" class="form-control @error('telefono') is-invalid @enderror" value="{{ old('telefono', $dueño?->telefono) }}" id="telefono" placeholder="Teléfono">
            @error('telefono')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="correo" class="form-label fw-bold">{{ __('Correo Electrónico') }}</label>
            <input type="email" name="correo" class="form-control @error('correo') is-invalid @enderror" value="{{ old('correo', $dueño?->correo) }}" id="correo" placeholder="Correo">
            @error('correo')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="ci" class="form-label fw-bold">{{ __('CI') }}</label>
            <input type="text" name="ci" class="form-control @error('ci') is-invalid @enderror" value="{{ old('ci', $dueño?->ci) }}" id="ci" placeholder="CI">
            @error('ci')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
    </div>
    <div class="col-md-12 mt-4 d-flex justify-content-between">
        <a href="{{ route('dueño.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn fw-bold" style="background-color:#E07B15; border-color:#E07B15; color:white;">{{ __('Guardar') }}</button>
    </div>
</div>