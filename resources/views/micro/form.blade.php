<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-bus-front" aria-hidden="true"></i>{{ $micro->exists ? 'Editar Micro' : 'Nuevo Micro' }}</h1>
        <p class="page-subtitle">Complete los datos del vehículo.</p>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="form-section">
            <div class="form-section-title">Asignaciones</div>
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label for="propietario_id" class="form-label">Propietario <span class="form-hint-required">*</span></label>
                    <select name="propietario_id" id="propietario_id" class="form-select @error('propietario_id') is-invalid @enderror" required>
                        <option value="">Seleccione un propietario...</option>
                        @foreach($propietarios as $p)
                            <option value="{{ $p->id }}" {{ old('propietario_id', $micro->propietario_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->nombre }} {{ $p->apellido }} — CI: {{ $p->ci }}
                            </option>
                        @endforeach
                    </select>
                    @error('propietario_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-6">
                    <label for="interno_id" class="form-label">N° Interno</label>
                    <select name="interno_id" id="interno_id" class="form-select @error('interno_id') is-invalid @enderror">
                        <option value="">Sin interno asignado</option>
                        @foreach($internos as $i)
                            <option value="{{ $i->id }}" {{ old('interno_id', $micro->interno_id) == $i->id ? 'selected' : '' }}>
                                {{ $i->numero_interno }} ({{ ucfirst($i->estado) }})
                            </option>
                        @endforeach
                    </select>
                    @error('interno_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-title">Datos del vehículo</div>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="placa" class="form-label">Placa <span class="form-hint-required">*</span></label>
                    <input type="text" name="placa" id="placa"
                           class="form-control @error('placa') is-invalid @enderror"
                           value="{{ old('placa', $micro->placa) }}"
                           placeholder="Ej: 1234-ABC" required>
                    @error('placa')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label for="marca" class="form-label">Marca <span class="form-hint-required">*</span></label>
                    <input type="text" name="marca" id="marca"
                           class="form-control @error('marca') is-invalid @enderror"
                           value="{{ old('marca', $micro->marca) }}"
                           placeholder="Ej: Toyota" required>
                    @error('marca')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label for="modelo" class="form-label">Modelo <span class="form-hint-required">*</span></label>
                    <input type="text" name="modelo" id="modelo"
                           class="form-control @error('modelo') is-invalid @enderror"
                           value="{{ old('modelo', $micro->modelo) }}"
                           placeholder="Ej: Coaster" required>
                    @error('modelo')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label for="chasis" class="form-label">Chasis</label>
                    <input type="text" name="chasis" id="chasis"
                           class="form-control @error('chasis') is-invalid @enderror"
                           value="{{ old('chasis', $micro->chasis) }}"
                           placeholder="N° de chasis">
                    @error('chasis')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label for="anio_fabricacion" class="form-label">Año de Fabricación</label>
                    <input type="number" name="anio_fabricacion" id="anio_fabricacion"
                           class="form-control @error('anio_fabricacion') is-invalid @enderror"
                           value="{{ old('anio_fabricacion', $micro->anio_fabricacion) }}"
                           placeholder="Ej: 2018" min="1980" max="{{ date('Y') }}">
                    @error('anio_fabricacion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="form-section">
            <div class="form-section-title">Capacidad y estado</div>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label for="capacidad_pasajeros" class="form-label">Capacidad <span class="form-hint-required">*</span></label>
                    <input type="number" name="capacidad_pasajeros" id="capacidad_pasajeros"
                           class="form-control @error('capacidad_pasajeros') is-invalid @enderror"
                           value="{{ old('capacidad_pasajeros', $micro->capacidad_pasajeros) }}"
                           placeholder="30" min="1" required>
                    @error('capacidad_pasajeros')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label for="estado" class="form-label">Estado <span class="form-hint-required">*</span></label>
                    <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                        <option value="activo" {{ old('estado', $micro->estado ?? 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $micro->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="{{ route('micro.index') }}" class="btn btn-outline-brand">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-brand">
            <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Guardar Micro
        </button>
    </div>
</div>
