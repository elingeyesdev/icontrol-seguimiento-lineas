{{-- Formulario compartido para crear/editar Asignación de Turno --}}
<div class="row g-3">
    <div class="col-md-6">
        <label for="fecha" class="form-label">
            <i class="bi bi-calendar3 me-1" aria-hidden="true"></i>Fecha <span class="form-hint-required">*</span>
        </label>
        <input type="date" name="fecha" id="fecha"
               class="form-control @error('fecha') is-invalid @enderror"
               value="{{ old('fecha', $asignacion->fecha ?? date('Y-m-d')) }}">
        @error('fecha')
            <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="turno_id" class="form-label">
            <i class="bi bi-clock me-1" aria-hidden="true"></i>Turno <span class="form-hint-required">*</span>
        </label>
        <select name="turno_id" id="turno_id" class="form-select @error('turno_id') is-invalid @enderror">
            <option value="">Seleccionar turno</option>
            @foreach($turnos as $turno)
                @php
                    $etiqueta = $turno->nombre_label
                        . ' (' . \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i')
                        . ' - ' . \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') . ')';
                @endphp
                <option value="{{ $turno->id }}"
                    {{ old('turno_id', $asignacion->turno_id ?? '') == $turno->id ? 'selected' : '' }}>
                    {{ $etiqueta }}
                </option>
            @endforeach
        </select>
        @error('turno_id')
            <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="micro_id" class="form-label">
            <i class="bi bi-bus-front me-1" aria-hidden="true"></i>Micro <span class="form-hint-required">*</span>
        </label>
        <select name="micro_id" id="micro_id" class="form-select @error('micro_id') is-invalid @enderror">
            <option value="">Seleccionar micro</option>
            @foreach($micros as $micro)
                <option value="{{ $micro->id }}"
                    {{ old('micro_id', $asignacion->micro_id ?? '') == $micro->id ? 'selected' : '' }}>
                    Interno {{ $micro->interno->numero_interno ?? 'Sin interno' }}
                    — {{ $micro->placa }}
                    — {{ $micro->marca }} {{ $micro->modelo }}
                </option>
            @endforeach
        </select>
        @error('micro_id')
            <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="conductor_id" class="form-label">
            <i class="bi bi-person-badge me-1" aria-hidden="true"></i>Conductor <span class="form-hint-required">*</span>
        </label>
        <select name="conductor_id" id="conductor_id" class="form-select @error('conductor_id') is-invalid @enderror">
            <option value="">Seleccionar conductor</option>
            @foreach($conductores as $conductor)
                <option value="{{ $conductor->id }}"
                    {{ old('conductor_id', $asignacion->conductor_id ?? '') == $conductor->id ? 'selected' : '' }}>
                    {{ $conductor->nombre }} {{ $conductor->apellido }} (CI: {{ $conductor->ci }})
                </option>
            @endforeach
        </select>
        @error('conductor_id')
            <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="ruta_id" class="form-label">
            <i class="bi bi-signpost-2 me-1" aria-hidden="true"></i>Ruta <span class="form-hint-required">*</span>
        </label>
        <select name="ruta_id" id="ruta_id" class="form-select @error('ruta_id') is-invalid @enderror">
            <option value="">Seleccionar ruta</option>
            @foreach($rutas as $ruta)
                <option value="{{ $ruta->id }}"
                    {{ old('ruta_id', $asignacion->ruta_id ?? '') == $ruta->id ? 'selected' : '' }}>
                    {{ $ruta->nombre }}
                </option>
            @endforeach
        </select>
        @error('ruta_id')
            <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
        @enderror
    </div>

    {{-- SDD Sección 13: al crear, el estado es pendiente y las horas las determina el flujo GPS. --}}
    @if($asignacion->exists)
        <div class="col-md-6">
            <label for="hora_salida" class="form-label">
                <i class="bi bi-play-circle me-1" aria-hidden="true"></i>Hora de salida
            </label>
            <input type="time" name="hora_salida" id="hora_salida"
                   class="form-control @error('hora_salida') is-invalid @enderror"
                   value="{{ old('hora_salida', isset($asignacion->hora_salida) ? \Carbon\Carbon::parse($asignacion->hora_salida)->format('H:i') : '') }}">
            @error('hora_salida')
                <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
            @enderror
            <div class="form-text">La hora de llegada se registra automáticamente al cumplir la última parada por GPS.</div>
        </div>

        <div class="col-md-6">
            <label for="estado" class="form-label">
                <i class="bi bi-toggle-on me-1" aria-hidden="true"></i>Estado <span class="form-hint-required">*</span>
            </label>
            <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                <option value="pendiente" {{ old('estado', $asignacion->estado ?? 'pendiente') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="en_curso" {{ old('estado', $asignacion->estado ?? 'pendiente') === 'en_curso' ? 'selected' : '' }}>En curso</option>
                <option value="completado" {{ old('estado', $asignacion->estado ?? 'pendiente') === 'completado' ? 'selected' : '' }}>Completado</option>
                <option value="retrasado" {{ old('estado', $asignacion->estado ?? 'pendiente') === 'retrasado' ? 'selected' : '' }}>Retrasado</option>
                <option value="cancelado" {{ old('estado', $asignacion->estado ?? 'pendiente') === 'cancelado' ? 'selected' : '' }}>Cancelado</option>
            </select>
            @error('estado')
                <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
            @enderror
        </div>
    @endif

    <div class="col-12">
        <label for="observaciones" class="form-label">
            <i class="bi bi-chat-text me-1" aria-hidden="true"></i>Observaciones <span class="text-muted fw-normal">(opcional)</span>
        </label>
        <textarea name="observaciones" id="observaciones" rows="3"
                  class="form-control @error('observaciones') is-invalid @enderror"
                  placeholder="Observaciones adicionales sobre esta asignación"
                  maxlength="1000">{{ old('observaciones', $asignacion->observaciones ?? '') }}</textarea>
        @error('observaciones')
            <div class="invalid-feedback"><strong>{{ $message }}</strong></div>
        @enderror
    </div>

    <div class="col-12 mt-2 d-flex flex-wrap justify-content-between gap-2">
        <a href="{{ route('asignacion-turno.index') }}" class="btn btn-outline-brand">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancelar
        </a>
        <button type="submit" class="btn btn-wine px-4">
            <i class="bi bi-check-lg me-1" aria-hidden="true"></i>{{ $asignacion->exists ? 'Guardar cambios' : 'Asignar turno' }}
        </button>
    </div>
</div>
