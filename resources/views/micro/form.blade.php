<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="form-group mb-3">
            <label for="dueño_id" class="form-label fw-bold">{{ __('Dueño') }}</label>
            <input type="text" name="dueño_id" class="form-control @error('dueño_id') is-invalid @enderror" value="{{ old('dueño_id', $micro?->dueño_id) }}" id="dueño_id" placeholder="ID del Dueño">
            @error('dueño_id')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="placa" class="form-label fw-bold">{{ __('Placa') }}</label>
            <input type="text" name="placa" class="form-control @error('placa') is-invalid @enderror" value="{{ old('placa', $micro?->placa) }}" id="placa" placeholder="Placa">
            @error('placa')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="modelo" class="form-label fw-bold">{{ __('Modelo') }}</label>
            <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" value="{{ old('modelo', $micro?->modelo) }}" id="modelo" placeholder="Modelo">
            @error('modelo')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="marca" class="form-label fw-bold">{{ __('Marca') }}</label>
            <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" value="{{ old('marca', $micro?->marca) }}" id="marca" placeholder="Marca">
            @error('marca')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="capacidad_pasajeros" class="form-label fw-bold">{{ __('Capacidad Pasajeros') }}</label>
            <input type="number" name="capacidad_pasajeros" class="form-control @error('capacidad_pasajeros') is-invalid @enderror" value="{{ old('capacidad_pasajeros', $micro?->capacidad_pasajeros) }}" id="capacidad_pasajeros" placeholder="Capacidad Pasajeros">
            @error('capacidad_pasajeros')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
    </div>
    <div class="col-md-12 mt-4 d-flex justify-content-between">
        <a href="{{ route('micro.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn fw-bold" style="background-color:#E07B15; border-color:#E07B15; color:white;">{{ __('Guardar') }}</button>
    </div>
</div>