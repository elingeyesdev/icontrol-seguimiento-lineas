<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-hdd-stack" aria-hidden="true"></i>{{ $interno->exists ? 'Editar Interno' : 'Nuevo Interno' }}</h1>
        <p class="page-subtitle">Complete los datos del número interno.</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="form-section">
            <div class="form-section-title">Identificación</div>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="numero_interno" class="form-label">N° Interno <span class="form-hint-required">*</span></label>
                    <input type="text" name="numero_interno" id="numero_interno"
                           class="form-control @error('numero_interno') is-invalid @enderror"
                           value="{{ old('numero_interno', $interno->numero_interno) }}"
                           placeholder="Ej: A-01" required>
                    @error('numero_interno')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label for="fecha_ingreso" class="form-label">Fecha de Ingreso <span class="form-hint-required">*</span></label>
                    <input type="datetime-local" name="fecha_ingreso" id="fecha_ingreso"
                           class="form-control @error('fecha_ingreso') is-invalid @enderror"
                           value="{{ old('fecha_ingreso', $interno->fecha_ingreso ? \Carbon\Carbon::parse($interno->fecha_ingreso)->format('Y-m-d\TH:i') : '') }}"
                           required>
                    @error('fecha_ingreso')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label for="estado" class="form-label">Estado <span class="form-hint-required">*</span></label>
                    <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                        <option value="disponible" {{ old('estado', $interno->estado ?? 'disponible') === 'disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="asignado" {{ old('estado', $interno->estado) === 'asignado' ? 'selected' : '' }}>Asignado</option>
                        <option value="inactivo" {{ old('estado', $interno->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-title">Observaciones</div>
            <div class="row g-3">
                <div class="col-12">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea name="observaciones" id="observaciones" rows="3"
                              class="form-control @error('observaciones') is-invalid @enderror"
                              placeholder="Observaciones sobre el estado o condición del interno...">{{ old('observaciones', $interno->observaciones) }}</textarea>
                    @error('observaciones')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="{{ route('interno.index') }}" class="btn btn-outline-brand">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-brand">
            <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Guardar Interno
        </button>
    </div>
</div>
