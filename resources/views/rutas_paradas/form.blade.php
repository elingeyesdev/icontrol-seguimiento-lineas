<div class="form-section">
    <div class="form-section-title">Ruta y parada</div>
    <div class="row g-3">
        <div class="col-12 col-md-6">
            <label for="ruta_id" class="form-label">Ruta <span class="form-hint-required">*</span></label>
            <select name="ruta_id" id="ruta_id" class="form-select @error('ruta_id') is-invalid @enderror" required>
                <option value="" disabled {{ old('ruta_id', $rutaParada->ruta_id) ? '' : 'selected' }}>Seleccione una ruta...</option>
                @foreach($rutas as $ruta)
                    <option value="{{ $ruta->id }}" {{ old('ruta_id', $rutaParada->ruta_id) == $ruta->id ? 'selected' : '' }}>
                        {{ $ruta->nombre }} @if($ruta->descripcion) ({{ $ruta->descripcion }}) @endif
                    </option>
                @endforeach
            </select>
            @error('ruta_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-6">
            <label for="parada_id" class="form-label">Parada <span class="form-hint-required">*</span></label>
            <select name="parada_id" id="parada_id" class="form-select @error('parada_id') is-invalid @enderror" required>
                <option value="" disabled {{ old('parada_id', $rutaParada->parada_id) ? '' : 'selected' }}>Seleccione una parada...</option>
                @foreach($paradas as $parada)
                    <option value="{{ $parada->id }}" {{ old('parada_id', $rutaParada->parada_id) == $parada->id ? 'selected' : '' }}>
                        {{ $parada->nombre }} ({{ $parada->referencia }})
                    </option>
                @endforeach
            </select>
            @error('parada_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">Orden y sentido</div>
    <div class="row g-3">
        <div class="col-12 col-md-4">
            <label for="orden" class="form-label">Orden <span class="form-hint-required">*</span></label>
            <input type="number" name="orden" id="orden" class="form-control @error('orden') is-invalid @enderror" value="{{ old('orden', $rutaParada->orden) }}" min="1" required>
            @error('orden')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-4">
            <label for="sentido" class="form-label">Sentido <span class="form-hint-required">*</span></label>
            <select name="sentido" id="sentido" class="form-select @error('sentido') is-invalid @enderror" required>
                <option value="Ida" {{ old('sentido', $rutaParada->sentido) === 'Ida' ? 'selected' : '' }}>Ida</option>
                <option value="Vuelta" {{ old('sentido', $rutaParada->sentido) === 'Vuelta' ? 'selected' : '' }}>Vuelta</option>
            </select>
            @error('sentido')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>

        <div class="col-12 col-md-4">
            <label for="estado" class="form-label">Estado <span class="form-hint-required">*</span></label>
            <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror" required>
                <option value="activo" {{ old('estado', $rutaParada->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $rutaParada->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between gap-2 mt-3">
    <a href="{{ route('rutas-paradas.index') }}" class="btn btn-outline-brand">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancelar
    </a>
    <button type="submit" class="btn btn-brand">
        <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Guardar Asignación
    </button>
</div>
