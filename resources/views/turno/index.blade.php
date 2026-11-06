@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="page-header">
        <div>
            <h1 class="page-title"><i class="bi bi-clock" aria-hidden="true"></i>Catálogo de Turnos</h1>
            <p class="page-subtitle">Parámetros de horarios: mañana, tarde y noche.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('turno.create') }}" class="btn btn-brand">
                <i class="bi bi-plus-lg me-1" aria-hidden="true"></i>Registrar Turno
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-soft-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2" aria-hidden="true"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif
    @if(session('info'))
        <div class="alert alert-soft-info alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2" aria-hidden="true"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Turno</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($turnos as $turno)
                            <tr>
                                <td class="text-muted">{{ $loop->iteration + ($turnos->currentPage() - 1) * $turnos->perPage() }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar"><i class="bi {{ $turno->turno_badge['icono'] }}" aria-hidden="true"></i></span>
                                        <span class="fw-semibold">{{ $turno->nombre_label }}</span>
                                    </div>
                                </td>
                                <td class="text-muted mono">{{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }}</td>
                                <td class="text-muted mono">{{ \Carbon\Carbon::parse($turno->hora_fin)->format('H:i') }}</td>
                                <td class="text-muted">{{ $turno->descripcion ?? '—' }}</td>
                                <td>
                                    <span class="badge-status {{ $turno->estado === 'activo' ? 'badge-status--active' : 'badge-status--inactive' }}">
                                        {{ $turno->estado_badge['label'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="{{ route('turno.show', $turno->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-eye me-1" aria-hidden="true"></i>Ver
                                        </a>
                                        <a href="{{ route('turno.edit', $turno->id) }}" class="btn btn-outline-brand btn-sm-action">
                                            <i class="bi bi-pencil-square me-1" aria-hidden="true"></i>Editar
                                        </a>
                                        @if($turno->estado === 'activo')
                                            <form action="{{ route('turno.destroy', $turno->id) }}" method="POST" class="d-inline" data-confirm="¿Eliminar el turno {{ $turno->nombre_label }}?">
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
                                <td colspan="7">
                                    <div class="empty-state">
                                        <i class="bi bi-clock" aria-hidden="true"></i>
                                        <div class="empty-state-title">Sin turnos registrados</div>
                                        <p class="mb-0">No se encontraron turnos con los criterios actuales.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($turnos->hasPages())
            <div class="card-footer">
                {{ $turnos->appends(['buscar' => $buscar])->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
