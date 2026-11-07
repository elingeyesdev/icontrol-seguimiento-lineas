@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-hdd-stack" aria-hidden="true"></i>Gestión de Internos</h1>
            <p class="page-subtitle">Administra los números internos de los vehículos.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('interno.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Registrar Interno
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-soft-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('interno.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-md-6 col-lg-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search" aria-hidden="true"></i></span>
                        <input type="text" name="buscar" class="form-control" placeholder="Buscar por número interno" value="{{ $buscar ?? '' }}" aria-label="Buscar internos">
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-brand">Buscar</button>
                </div>
                @if(!empty($buscar))
                    <div class="col-auto">
                        <a href="{{ route('interno.index') }}" class="btn btn-outline-brand btn-icon" aria-label="Limpiar búsqueda">
                            <i class="bi bi-x-lg" aria-hidden="true"></i>
                        </a>
                    </div>
                @endif
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>N° Interno</th>
                            <th>Fecha Ingreso</th>
                            <th>Observaciones</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($internos as $interno)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration + ($internos->currentPage() - 1) * $internos->perPage() }}</td>
                                <td><span class="fw-semibold mono">{{ $interno->numero_interno }}</span></td>
                                <td class="text-muted">{{ \Carbon\Carbon::parse($interno->fecha_ingreso)->format('d/m/Y') }}</td>
                                <td class="text-muted">{{ Str::limit($interno->observaciones ?? '—', 50) }}</td>
                                <td>
                                    @if($interno->estado === 'disponible')
                                        <span class="badge-status badge-status--active">{{ $interno->estado_badge['label'] }}</span>
                                    @elseif($interno->estado === 'asignado')
                                        <span class="badge-status badge-status--progress">{{ $interno->estado_badge['label'] }}</span>
                                    @else
                                        <span class="badge-status badge-status--inactive">{{ $interno->estado_badge['label'] }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('interno.show', $interno->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-eye me-1" aria-hidden="true"></i>Ver
                                        </a>
                                        <a href="{{ route('interno.edit', $interno->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                                        </a>
                                        @if($interno->estado !== 'inactivo')
                                            <form action="{{ route('interno.destroy', $interno->id) }}" method="POST" class="d-inline" data-confirm="¿Eliminar este interno?">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm-action">
                                                    <i class="bi bi-trash me-1" aria-hidden="true"></i>Eliminar
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-hdd-stack" aria-hidden="true"></i>
                                        <div class="empty-state-title">Sin internos registrados</div>
                                        <p class="mb-0">No se encontraron internos con los criterios actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($internos->hasPages())
            <div class="card-footer">
                {{ $internos->appends(['buscar' => $buscar])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
