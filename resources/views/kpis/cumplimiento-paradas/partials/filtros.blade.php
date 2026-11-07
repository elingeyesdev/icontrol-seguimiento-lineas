<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('kpis.cumplimiento-paradas.index') }}" class="row g-3 align-items-end">
            <div class="col-6 col-lg-2"><label class="form-label" for="fecha_inicio">Fecha inicial</label><input class="form-control" type="date" id="fecha_inicio" name="fecha_inicio" value="{{ $filtros['fecha_inicio'] ?? '' }}"></div>
            <div class="col-6 col-lg-2"><label class="form-label" for="fecha_fin">Fecha final</label><input class="form-control" type="date" id="fecha_fin" name="fecha_fin" value="{{ $filtros['fecha_fin'] ?? '' }}"></div>
            <div class="col-6 col-lg-2"><label class="form-label" for="ruta_id">Ruta</label><select class="form-select" id="ruta_id" name="ruta_id"><option value="">Todas</option>@foreach($rutas as $ruta)<option value="{{ $ruta->id }}" @selected(($filtros['ruta_id'] ?? null) == $ruta->id)>{{ $ruta->nombre }}</option>@endforeach</select></div>
            <div class="col-6 col-lg-2"><label class="form-label" for="conductor_id">Conductor</label><select class="form-select" id="conductor_id" name="conductor_id"><option value="">Todos</option>@foreach($conductores as $conductor)<option value="{{ $conductor->id }}" @selected(($filtros['conductor_id'] ?? null) == $conductor->id)>{{ $conductor->nombre_completo }}</option>@endforeach</select></div>
            <div class="col-6 col-lg-2"><label class="form-label" for="micro_id">Micro</label><select class="form-select" id="micro_id" name="micro_id"><option value="">Todos</option>@foreach($micros as $micro)<option value="{{ $micro->id }}" @selected(($filtros['micro_id'] ?? null) == $micro->id)>{{ $micro->placa }}</option>@endforeach</select></div>
            <div class="col-6 col-lg-2"><label class="form-label" for="turno_id">Turno</label><select class="form-select" id="turno_id" name="turno_id"><option value="">Todos</option>@foreach($turnos as $turno)<option value="{{ $turno->id }}" @selected(($filtros['turno_id'] ?? null) == $turno->id)>{{ ucfirst($turno->nombre) }}</option>@endforeach</select></div>
            <div class="col-6 col-lg-2"><label class="form-label" for="sentido">Sentido</label><select class="form-select" id="sentido" name="sentido"><option value="">Todos</option><option value="Ida" @selected(($filtros['sentido'] ?? null) === 'Ida')>Ida</option><option value="Vuelta" @selected(($filtros['sentido'] ?? null) === 'Vuelta')>Vuelta</option></select></div>
            <div class="col-6 col-lg-2"><button class="btn btn-brand w-100" type="submit">Consultar</button></div>
        </form>
    </div>
</div>
