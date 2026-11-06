@vite('resources/css/catalogos.css')
<div class="form-section">
    <div class="form-section-title">Información de la parada</div>
    <div class="row g-3">
        <div class="col-12 col-md-5">
            <label for="nombre" class="form-label">Nombre de la Parada <span class="form-hint-required">*</span></label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                <input type="text" name="nombre" id="nombre"
                       class="form-control @error('nombre') is-invalid @enderror"
                       value="{{ old('nombre', $parada->nombre) }}"
                       placeholder="Ej: Parada Central" required>
            </div>
            @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-5">
            <label for="referencia" class="form-label">Referencia</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-signpost-2" aria-hidden="true"></i></span>
                <input type="text" name="referencia" id="referencia"
                       class="form-control @error('referencia') is-invalid @enderror"
                       value="{{ old('referencia', $parada->referencia) }}"
                       placeholder="Ej: Frente al mercado principal">
            </div>
            @error('referencia')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
        <div class="col-12 col-md-2">
            <label for="estado" class="form-label">Estado <span class="form-hint-required">*</span></label>
            <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                <option value="activo" {{ old('estado', $parada->estado) === 'activo' ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $parada->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">Ubicación geográfica</div>
    <p class="form-text mb-3">
        <i class="bi bi-info-circle me-1" aria-hidden="true"></i>
        Busque una ubicación o haga clic directamente en el mapa para seleccionar la posición de la parada.
    </p>

    <div class="row g-2 mb-3">
        <div class="col-12 col-md">
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                <input type="text" id="searchLocation" class="form-control"
                       placeholder="Buscar ubicación... (Ej: Plaza 24 de Septiembre, Santa Cruz)"
                       aria-label="Buscar ubicación">
            </div>
        </div>
        <div class="col-12 col-md-auto">
            <button type="button" id="btnSearch" class="btn btn-outline-brand">
                <i class="bi bi-search me-1" aria-hidden="true"></i>Buscar
            </button>
        </div>
    </div>

    <div id="searchResults" class="list-group parada-search-results mb-3 d-none"></div>

    <div class="border rounded-3 overflow-hidden mb-3">
        <div id="paradaMap" class="parada-map"
     data-lat="{{ old('latitud', $parada->latitud) }}"
     data-lng="{{ old('longitud', $parada->longitud) }}"
     data-has-location="{{ ($parada->latitud && $parada->longitud) || old('latitud') ? 'true' : 'false' }}"></div>
    </div>

    <input type="hidden" name="latitud" id="latitud" value="{{ old('latitud', $parada->latitud) }}">
    <input type="hidden" name="longitud" id="longitud" value="{{ old('longitud', $parada->longitud) }}">

    @error('latitud')
        <div class="text-danger small mt-1"><i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>{{ $message }}</div>
    @enderror
    @error('longitud')
        <div class="text-danger small mt-1"><i class="bi bi-exclamation-triangle me-1" aria-hidden="true"></i>{{ $message }}</div>
    @enderror
</div>

<div class="d-flex flex-wrap justify-content-between gap-2">
    <a href="{{ route('parada.index') }}" class="btn btn-outline-brand">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancelar
    </a>
    <button type="submit" class="btn btn-brand">
        <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Guardar Parada
    </button>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@vite('resources/js/parada-map.js')
