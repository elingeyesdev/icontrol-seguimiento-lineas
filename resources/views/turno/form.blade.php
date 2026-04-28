<div class="row padding-1 p-1">
    <div class="col-md-12">
        <div class="form-group mb-3">
            <label for="interno_id" class="form-label fw-bold">{{ __('Interno ID') }}</label>
            <input type="text" name="interno_id" class="form-control @error('interno_id') is-invalid @enderror" value="{{ old('interno_id', $turno?->interno_id) }}" id="interno_id" placeholder="ID del Interno">
            @error('interno_id')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="ruta_id" class="form-label fw-bold">{{ __('Ruta ID') }}</label>
            <input type="text" name="ruta_id" class="form-control @error('ruta_id') is-invalid @enderror" value="{{ old('ruta_id', $turno?->ruta_id) }}" id="ruta_id" placeholder="ID de la Ruta">
            @error('ruta_id')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="hora_inicio" class="form-label fw-bold">{{ __('Hora Inicio') }}</label>
            <input type="time" name="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror" value="{{ old('hora_inicio', $turno?->hora_inicio) }}" id="hora_inicio">
            @error('hora_inicio')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="hora_fin" class="form-label fw-bold">{{ __('Hora Fin') }}</label>
            <input type="time" name="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror" value="{{ old('hora_fin', $turno?->hora_fin) }}" id="hora_fin">
            @error('hora_fin')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
        <div class="form-group mb-3">
            <label for="fecha_laboral" class="form-label fw-bold">{{ __('Fecha Laboral') }}</label>
            <input type="date" name="fecha_laboral" class="form-control @error('fecha_laboral') is-invalid @enderror" value="{{ old('fecha_laboral', $turno?->fecha_laboral) }}" id="fecha_laboral">
            @error('fecha_laboral')
                <div class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
    </div>
    <div class="col-md-12 mt-4 d-flex justify-content-between">
        <a href="{{ route('turno.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn fw-bold" style="background-color:#E07B15; border-color:#E07B15; color:white;">{{ __('Guardar') }}</button>
    </div>
</div>