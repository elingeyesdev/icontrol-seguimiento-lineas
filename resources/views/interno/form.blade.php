<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="form-group mb-3">
            <label for="estado" class="form-label fw-bold">{{ __('Estado') }}</label>
            <input type="text" name="estado" class="form-control @error('estado') is-invalid @enderror" value="{{ old('estado', $interno?->estado) }}" id="estado" placeholder="Estado (Activo, Inactivo, etc.)">
            @error('estado')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="micro_id" class="form-label fw-bold">{{ __('Micro ID') }}</label>
            <input type="text" name="micro_id" class="form-control @error('micro_id') is-invalid @enderror" value="{{ old('micro_id', $interno?->micro_id) }}" id="micro_id" placeholder="ID del Micro">
            @error('micro_id')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="conductor_id" class="form-label fw-bold">{{ __('Conductor ID') }}</label>
            <input type="text" name="conductor_id" class="form-control @error('conductor_id') is-invalid @enderror" value="{{ old('conductor_id', $interno?->conductor_id) }}" id="conductor_id" placeholder="ID del Conductor">
            @error('conductor_id')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="fecha_ingreso" class="form-label fw-bold">{{ __('Fecha de Ingreso') }}</label>
            <input type="date" name="fecha_ingreso" class="form-control @error('fecha_ingreso') is-invalid @enderror" value="{{ old('fecha_ingreso', $interno?->fecha_ingreso) }}" id="fecha_ingreso">
            @error('fecha_ingreso')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
    </div>
    <div class="col-md-12 mt-4 d-flex justify-content-between">
        <a href="{{ route('interno.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn fw-bold" style="background-color:#E07B15; border-color:#E07B15; color:white;">{{ __('Guardar') }}</button>
    </div>
</div>