@extends('layouts.app')

@section('content')
@vite('resources/css/catalogos.css')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('parada.index') }}" class="text-decoration-none">Paradas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $parada->nombre }}</li>
                </ol>
            </nav>

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-geo-alt" aria-hidden="true"></i>Detalle de Parada</h1>
                    <p class="page-subtitle">Información registrada de la parada.</p>
                </div>
                <div class="page-actions">
                    <a href="{{ route('parada.edit', $parada->id) }}" class="btn btn-outline-brand">
                        <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                    </a>
                    <form action="{{ route('parada.destroy', $parada->id) }}" method="POST" class="d-inline" data-confirm="¿Está seguro de eliminar esta parada?">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="bi bi-trash me-1" aria-hidden="true"></i>Eliminar
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="panel-muted p-3">
                        <div class="form-section-title">Información general</div>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <div class="meta-label">Nombre</div>
                                <div class="meta-value">{{ $parada->nombre }}</div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="meta-label">Referencia</div>
                                <div class="meta-value">{{ $parada->referencia ?? '—' }}</div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="meta-label">Estado</div>
                                <div class="meta-value">
                                    @if($parada->estado === 'activo')
                                        <span class="badge-status badge-status--active">Activo</span>
                                    @else
                                        <span class="badge-status badge-status--inactive">Inactivo</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($parada->latitud && $parada->longitud)
                        <div class="panel-muted p-3 mt-3">
                            <div class="form-section-title">Ubicación en el mapa</div>
                            <div class="border rounded-3 overflow-hidden">
                                <div id="showMap" class="parada-map"
                                     data-lat="{{ $parada->latitud }}"
                                     data-lng="{{ $parada->longitud }}"
                                     data-nombre="{{ $parada->nombre }}"
                                     data-referencia="{{ $parada->referencia }}"></div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="card-footer">
                    <a href="{{ route('parada.index') }}" class="btn btn-outline-brand">
                        <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver a la lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@if($parada->latitud && $parada->longitud)
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@vite('resources/js/parada-map.js')
@endif
@endsection
