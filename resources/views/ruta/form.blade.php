@vite('resources/css/catalogos.css')

<div class="page-header">
    <div>
        <h1 class="page-title"><i class="bi bi-signpost-2" aria-hidden="true"></i>{{ $ruta->exists ? 'Editar Ruta' : 'Nueva Ruta' }}</h1>
        <p class="page-subtitle">Complete la información general y configure las paradas de ida y vuelta.</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-body">
        <div class="form-section">
            <div class="form-section-title">Información general</div>
            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <label for="nombre" class="form-label">Nombre de la Ruta <span class="form-hint-required">*</span></label>
                    <input type="text" name="nombre" id="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $ruta->nombre) }}"
                           placeholder="Ej: Línea 61 - Troncal Central" required>
                    @error('nombre')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12 col-md-4">
                    <label for="estado" class="form-label">Estado <span class="form-hint-required">*</span></label>
                    <select name="estado" id="estado" class="form-select @error('estado') is-invalid @enderror">
                        <option value="activo" {{ old('estado', $ruta->estado ?? 'activo') === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ old('estado', $ruta->estado) === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estado')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label for="descripcion" class="form-label">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="2"
                              class="form-control @error('descripcion') is-invalid @enderror"
                              placeholder="Descripción del recorrido de la ruta (opcional)">{{ old('descripcion', $ruta->descripcion) }}</textarea>
                    @error('descripcion')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <div>
                <div class="section-title">Parametrización del Recorrido</div>
                <small class="text-muted">Configure las paradas ordenadas para el sentido de IDA y de VUELTA.</small>
            </div>
            <div class="d-flex gap-2">
                <span class="badge badge-status--progress">
                    Ida: <strong id="contadorIda">0</strong>
                </span>
                <span class="badge badge-status--pending">
                    Vuelta: <strong id="contadorVuelta">0</strong>
                </span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-lg-4">
                <div class="panel-muted h-100 d-flex flex-column">
                    <div class="p-3 border-bottom">
                        <div class="form-section-title mb-2">
                            <i class="bi bi-collection me-1" aria-hidden="true"></i>Catálogo de paradas
                        </div>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                            <input type="text" id="searchParada" class="form-control" placeholder="Buscar parada..." aria-label="Buscar parada">
                        </div>
                    </div>
                    <div id="listaDisponibles" class="p-2 ruta-paradas-list">
                        @foreach($paradas as $parada)
                            <div class="parada-catalogo d-flex align-items-center justify-content-between gap-2 p-2 mb-1 rounded-2 bg-white border"
                                 data-id="{{ $parada->id }}" data-nombre="{{ $parada->nombre }}" data-referencia="{{ $parada->referencia }}">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <span class="text-brand flex-shrink-0"><i class="bi bi-geo-alt" aria-hidden="true"></i></span>
                                    <div class="text-truncate">
                                        <div class="fw-semibold text-truncate small" title="{{ $parada->nombre }}">{{ $parada->nombre }}</div>
                                        @if($parada->referencia)
                                            <small class="text-muted text-truncate d-block">{{ $parada->referencia }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="btn-group btn-group-sm ms-2 flex-shrink-0">
                                    <button type="button" class="btn btn-outline-brand btn-add-ida" title="Agregar a ida" aria-label="Agregar {{ $parada->nombre }} a ida">
                                        + Ida
                                    </button>
                                    <button type="button" class="btn btn-outline-brand btn-add-vuelta" title="Agregar a vuelta" aria-label="Agregar {{ $parada->nombre }} a vuelta">
                                        + Vta
                                    </button>
                                </div>
                            </div>
                        @endforeach
                        <div id="noParadasMsg" class="empty-state py-3 d-none">
                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                            <p class="mb-0 small">No se encontraron paradas.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="panel-muted h-100 d-flex flex-column">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="form-section-title mb-0">
                            <i class="bi bi-arrow-right-circle me-1" aria-hidden="true"></i>Paradas IDA
                        </div>
                        <span id="badgeIdaCount" class="badge badge-status--progress">0</span>
                    </div>
                    <div id="listaIda" class="p-2 ruta-paradas-list">
                        <div id="emptyIdaMsg" class="empty-state py-4">
                            <i class="bi bi-arrow-right-square" aria-hidden="true"></i>
                            <p class="mb-0 small">Agregue paradas de ida desde el catálogo.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="panel-muted h-100 d-flex flex-column">
                    <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                        <div class="form-section-title mb-0">
                            <i class="bi bi-arrow-left-circle me-1" aria-hidden="true"></i>Paradas VUELTA
                        </div>
                        <span id="badgeVueltaCount" class="badge badge-status--pending">0</span>
                    </div>
                    <div id="listaVuelta" class="p-2 ruta-paradas-list">
                        <div id="emptyVueltaMsg" class="empty-state py-4">
                            <i class="bi bi-arrow-left-square" aria-hidden="true"></i>
                            <p class="mb-0 small">Agregue paradas de vuelta desde el catálogo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="hiddenIdaInputs"></div>
<div id="hiddenVueltaInputs"></div>

<div class="d-flex flex-wrap justify-content-between gap-2">
    <a href="{{ route('ruta.index') }}" class="btn btn-outline-brand">
        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Cancelar
    </a>
    <button type="submit" class="btn btn-brand">
        <i class="bi bi-check-lg me-1" aria-hidden="true"></i>Guardar Ruta Completa
    </button>
</div>

<div id="rutaFormConfig" hidden
     data-paradas-ida="{{ json_encode($paradasIda ?? [], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) }}"
     data-paradas-vuelta="{{ json_encode($paradasVuelta ?? [], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP) }}"></div>

@vite('resources/js/ruta-form.js')