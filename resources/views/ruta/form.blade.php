<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="form-group mb-3">
            <label for="origen" class="form-label fw-bold">{{ __('Origen') }}</label>
            <input type="text" name="origen" class="form-control @error('origen') is-invalid @enderror" value="{{ old('origen', $ruta?->origen) }}" id="origen" placeholder="Origen">
            @error('origen')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="destino" class="form-label fw-bold">{{ __('Destino') }}</label>
            <input type="text" name="destino" class="form-control @error('destino') is-invalid @enderror" value="{{ old('destino', $ruta?->destino) }}" id="destino" placeholder="Destino">
            @error('destino')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="nombre_ruta" class="form-label fw-bold">{{ __('Nombre Ruta') }}</label>
            <input type="text" name="nombre_ruta" class="form-control @error('nombre_ruta') is-invalid @enderror" value="{{ old('nombre_ruta', $ruta?->nombre_ruta) }}" id="nombre_ruta" placeholder="Nombre Ruta">
            @error('nombre_ruta')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
    </div>
    <div class="col-md-12 mt-4 d-flex justify-content-between">
        <a href="{{ route('ruta.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn fw-bold" style="background-color:#E07B15; border-color:#E07B15; color:white;">{{ __('Guardar') }}</button>
    </div>
</div>