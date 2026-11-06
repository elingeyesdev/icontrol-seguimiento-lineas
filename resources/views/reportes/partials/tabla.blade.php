@php
    $clasesEstado = [
        'Activo' => 'badge-status--active',
        'Completado' => 'badge-status--active',
        'En curso' => 'badge-status--progress',
        'Pendiente' => 'badge-status--pending',
        'Retrasado' => 'badge-status--warning',
        'Cancelado' => 'badge-status--inactive',
        'Inactivo' => 'badge-status--inactive',
    ];
@endphp
<div class="card reportes-table-card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-list-check text-brand" aria-hidden="true"></i>
            <span class="fw-semibold">{{ $titulo }}</span>
            <span class="badge-status reportes-count-badge">{{ $filas->total() }} {{ $filas->total() === 1 ? 'registro' : 'registros' }}</span>
        </div>
        @if(!empty($filtrosAplicados))
            <div class="d-flex flex-wrap gap-1">
                @foreach($filtrosAplicados as $etiqueta => $valor)
                    <span class="reportes-filter-badge">{{ $etiqueta }}: {{ $valor }}</span>
                @endforeach
            </div>
        @endif
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 reportes-table">
                <thead>
                    <tr>
                        @foreach($columnas as $etiqueta)
                            <th>{{ $etiqueta }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($filas as $fila)
                        <tr>
                            @foreach($claves as $clave)
                                <td class="{{ $loop->first ? 'fw-semibold' : 'text-muted' }}">
                                    @if($clave === 'estado')
                                        <span class="badge-status {{ $clasesEstado[$fila[$clave]] ?? 'badge-status--inactive' }}">
                                            {{ $fila[$clave] }}
                                        </span>
                                    @elseif($clave === 'origen')
                                        <span class="badge-status {{ $fila[$clave] === 'Registrado' ? 'badge-status--progress' : 'badge-status--inactive' }}">
                                            {{ $fila[$clave] }}
                                        </span>
                                    @else
                                        {{ $fila[$clave] }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($columnas) }}">
                                <div class="empty-state">
                                    <i class="bi bi-inbox" aria-hidden="true"></i>
                                    <div class="empty-state-title">Sin resultados para los filtros aplicados</div>
                                    <p class="mb-0">Ajusta el período o los criterios de búsqueda y vuelve a consultar.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($filas->hasPages())
        <div class="card-footer reportes-pagination">
            {{ $filas->appends($filtros)->links() }}
        </div>
    @endif
</div>
