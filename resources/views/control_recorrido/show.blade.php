@extends('layouts.app')

@section('content')
@vite('resources/css/control-recorrido.css')

@php
    $estadoClases = [
        'pendiente' => 'badge-status--pending',
        'en_curso' => 'badge-status--progress',
        'completado' => 'badge-status--active',
        'retrasado' => 'badge-status--warning',
        'cancelado' => 'badge-status--cancelled',
    ];
    $estadoClase = $estadoClases[$asignacion->estado] ?? 'badge-status--inactive';

    $badgesParada = [
        'cumplido' => 'badge-status--active',
        'en_proceso' => 'badge-status--warning',
        'omitido' => 'badge-status--danger',
        'pendiente' => 'badge-status--inactive',
    ];
    $etiquetasParada = [
        'cumplido' => 'Cumplida',
        'en_proceso' => 'En proceso',
        'omitido' => 'Omitida',
        'pendiente' => 'Pendiente',
    ];

    $sentidos = [
        'Ida' => $filasIda,
        'Vuelta' => $filasVuelta,
    ];
    $sentidoActivo = in_array($sentidoActivo, ['Ida', 'Vuelta'], true) ? $sentidoActivo : 'Ida';
@endphp

<div class="container-fluid py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('control-recorrido.index') }}" class="text-decoration-none">Control de recorrido</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $codigo }}</li>
        </ol>
    </nav>

    <div class="page-header">
        <div>
            <h1 class="page-title">
                <i class="bi bi-signpost-split" aria-hidden="true"></i>
                Detalle de Recorrido - {{ $codigo }}
            </h1>
            <div class="d-flex flex-wrap align-items-center gap-2 mt-2">
                <span class="badge-status {{ $estadoClase }}">{{ ucfirst(str_replace('_', ' ', $asignacion->estado)) }}</span>
                <span class="small text-muted">{{ $paradasCumplidas }} de {{ $totalParadas }} paradas · {{ $porcentaje }}%</span>
            </div>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2 mb-4">
        <span class="meta-chip"><i class="bi bi-calendar3 me-1" aria-hidden="true"></i><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($asignacion->fecha)->format('d/m/Y') }}</span>
        <span class="meta-chip"><i class="bi bi-clock me-1" aria-hidden="true"></i><strong>Turno:</strong> {{ ucfirst($asignacion->turno->nombre ?? '—') }}</span>
        <span class="meta-chip"><i class="bi bi-signpost-2 me-1" aria-hidden="true"></i><strong>Ruta:</strong> {{ $asignacion->ruta->nombre ?? '—' }}</span>
        <span class="meta-chip"><i class="bi bi-bus-front me-1" aria-hidden="true"></i><strong>Micro:</strong> {{ $asignacion->micro->placa ?? 'S/P' }} · Int. {{ $asignacion->micro->interno->numero_interno ?? 'S/I' }}</span>
        <span class="meta-chip"><i class="bi bi-person-badge me-1" aria-hidden="true"></i><strong>Conductor:</strong> {{ trim(($asignacion->conductor?->nombre ?? '').' '.($asignacion->conductor?->apellido ?? '')) ?: '—' }}</span>
        <span class="meta-chip"><i class="bi bi-flag me-1" aria-hidden="true"></i><strong>Salida:</strong> {{ $asignacion->hora_salida ?? 'Sin registro' }}</span>
    </div>

    <div class="card overflow-hidden">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <div class="fw-semibold">
                    <i class="bi bi-list-ol me-1" aria-hidden="true"></i>
                    Secuencia de paradas
                </div>
                <div class="text-muted small">Hora real de paso registrada por GPS.</div>
            </div>
            <ul class="nav cr-route-tabs" role="tablist">
                @foreach($sentidos as $sentido => $filas)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $sentidoActivo === $sentido ? 'active' : '' }}"
                                id="tab-{{ strtolower($sentido) }}"
                                data-bs-toggle="tab"
                                data-bs-target="#pane-{{ strtolower($sentido) }}"
                                type="button"
                                role="tab"
                                aria-controls="pane-{{ strtolower($sentido) }}"
                                aria-selected="{{ $sentidoActivo === $sentido ? 'true' : 'false' }}">
                            <i class="bi bi-arrow-{{ $sentido === 'Ida' ? 'right' : 'left' }} me-1" aria-hidden="true"></i>
                            {{ $sentido }}
                            <span class="cr-route-tabs__count">{{ $filas->count() }}</span>
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="tab-content" id="sentidoTabsContent">
            @foreach($sentidos as $sentido => $filas)
                @php
                    $cumplidas = $filas->where('estado', 'cumplido')->count();
                    $enProceso = $filas->where('estado', 'en_proceso')->count();
                    $omitidas = $filas->where('estado', 'omitido')->count();
                    $pendientes = $filas->where('estado', 'pendiente')->count();
                @endphp

                <div class="tab-pane fade {{ $sentidoActivo === $sentido ? 'show active' : '' }}"
                     id="pane-{{ strtolower($sentido) }}"
                     role="tabpanel"
                     aria-labelledby="tab-{{ strtolower($sentido) }}"
                     tabindex="0">

                    <div class="p-3">
                        <div class="row row-cols-2 row-cols-sm-3 row-cols-lg-5 g-2">
                            <div class="col">
                                <div class="cr-stat-box cr-stat-box--success">
                                    <div class="cr-stat-box__value">{{ $cumplidas }}</div>
                                    <div class="meta-label">Cumplidas</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="cr-stat-box cr-stat-box--warning">
                                    <div class="cr-stat-box__value">{{ $enProceso }}</div>
                                    <div class="meta-label">En proceso</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="cr-stat-box cr-stat-box--danger">
                                    <div class="cr-stat-box__value">{{ $omitidas }}</div>
                                    <div class="meta-label">Omitidas</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="cr-stat-box">
                                    <div class="cr-stat-box__value">{{ $pendientes }}</div>
                                    <div class="meta-label">Pendientes</div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="cr-stat-box cr-stat-box--total">
                                    <div class="cr-stat-box__value">{{ $filas->count() }}</div>
                                    <div class="meta-label">Total</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cr-stop-list">
                        @forelse($filas as $fila)
                            @php
                                $estadoParada = $fila['estado'];
                                $badgeParada = $badgesParada[$estadoParada] ?? 'badge-status--inactive';
                                $etiquetaParada = $etiquetasParada[$estadoParada] ?? ucfirst($estadoParada);
                            @endphp
                            <div class="cr-stop-row {{ $estadoParada === 'en_proceso' ? 'is-current' : '' }}">
                                <span class="cr-sequence is-{{ $estadoParada }}">{{ $fila['numero'] }}</span>

                                <div class="cr-stop-main">
                                    <div class="fw-semibold text-truncate">{{ $fila['parada']?->nombre ?? 'Parada sin nombre' }}</div>
                                    @if($fila['parada']?->referencia)
                                        <div class="text-muted small text-truncate">{{ $fila['parada']->referencia }}</div>
                                    @endif
                                </div>

                                <div class="text-end flex-shrink-0">
                                    <div class="mono {{ $fila['hora'] ? 'fw-semibold' : 'text-muted' }}">{{ $fila['hora'] ?? '—' }}</div>
                                    <span class="badge-status {{ $badgeParada }}">{{ $etiquetaParada }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state py-4">
                                <i class="bi bi-filter-circle" aria-hidden="true"></i>
                                <div class="empty-state-title">Sin paradas en {{ $sentido }}</div>
                                <p class="mb-0">Esta ruta no tiene paradas configuradas para este sentido.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card-footer d-flex flex-wrap align-items-center gap-2 small text-muted">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <span>La hora mostrada corresponde al paso real detectado por GPS. Las paradas pendientes se muestran con «—» hasta que el micro las alcance.</span>
            @if($siguienteParada)
                <span class="ms-auto badge-status badge-status--warning">Próxima: {{ $siguienteParada->parada?->nombre ?? 'Sin nombre' }} ({{ $siguienteParada->sentido }})</span>
            @elseif($paradasPendientes === 0)
                <span class="ms-auto badge-status badge-status--active">Recorrido cerrado</span>
            @endif
        </div>
    </div>
</div>
@endsection
