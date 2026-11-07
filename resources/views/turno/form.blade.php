<div class="form-section">
    <div class="form-section-title">Horario</div>
    <div class="row g-3">
        <div class="col-12">
            <label for="nombre" class="form-label">Turno <span class="form-hint-required">*</span></label>
            <select name="nombre" id="nombre"
                    class="form-select @error('nombre') is-invalid @enderror">
                <option value="">— Seleccionar turno —</option>
                <option value="mañana" {{ old('nombre', $turno->nombre ?? '') === 'mañana' ? 'selected' : '' }}>
                    Mañana
                </option>
                <option value="tarde" {{ old('nombre', $turno->nombre ?? '') === 'tarde' ? 'selected' : '' }}>
                    Tarde
                </option>
                <option value="noche" {{ old('nombre', $turno->nombre ?? '') === 'noche' ? 'selected' : '' }}>
                    Noche
                </option>
            </select>
            @error('nombre')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label for="hora_inicio" class="form-label">Hora de inicio <span class="form-hint-required">*</span></label>
            <input type="time" name="hora_inicio" id="hora_inicio"
                   class="form-control @error('hora_inicio') is-invalid @enderror"
                   value="{{ old('hora_inicio', isset($turno->hora_inicio) ? \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') : '') }}">
            @error('hora_inicio')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label for="hora_fin" class="form-label">Hora de fin <span class="form-hint-required">*</span></label>
            <input type="time" name="hora_fin" id="hora_fin"
                   class="form-control @error('hora_fin') is-invalid @enderror"
                   value="{{ old('hora_fin', isset($turno->hora_fin) ? \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') : '') }}">
            @error('hora_fin')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="form-section">
    <div class="form-section-title">Detalles y estado</div>
    <div class="row g-3">
        <div class="col-12">
            <label for="descripcion" class="form-label">Descripción <span class="text-muted fw-normal">(opcional)</span></label>
            <input type="text" name="descripcion" id="descripcion"
                   class="form-control @error('descripcion') is-invalid @enderror"
                   value="{{ old('descripcion', $turno->descripcion ?? '') }}"
                   placeholder="Ej. Turno de mañana, inicia a las 5am"
                   maxlength="255">
            @error('descripcion')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12 col-md-6">
            <label for="estado" class="form-label">Estado <span class="form-hint-required">*</span></label>
            <select name="estado" id="estado"
                    class="form-select @error('estado') is-invalid @enderror">
                <option value="activo"   {{ old('estado', $turno->estado ?? 'activo') === 'activo'   ? 'selected' : '' }}>Activo</option>
                <option value="inactivo" {{ old('estado', $turno->estado ?? 'activo') === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
            </select>
            @error('estado')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<div class="d-flex flex-wrap justify-content-between gap-2 mt-3">
    <a href="{{ route('turno.index') }}" class="btn btn-outline-brand">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancelar
    </a>
    <button type="submit" class="btn btn-brand">
        <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Guardar
    </button>
</div>
