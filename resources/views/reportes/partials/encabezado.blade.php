<div class="page-header">
    <div class="d-flex align-items-start gap-3">
        <img src="{{ asset('images/logo-linea61.png') }}" alt="Línea 61" class="reportes-logo">
        <div>
            <h1 class="page-title">
                <i class="bi bi-file-earmark-bar-graph" aria-hidden="true"></i>{{ $titulo }}
            </h1>
            <p class="page-subtitle">{{ $descripcion }}</p>
        </div>
    </div>
    <div class="page-actions reportes-acciones">
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-brand reportes-volver">
            <i class="bi bi-arrow-left me-1" aria-hidden="true"></i>Volver
        </a>
        <button type="button" class="btn btn-outline-brand" data-print>
            <i class="bi bi-printer me-1" aria-hidden="true"></i>Imprimir
        </button>
        <a href="{{ route('reportes.pdf', array_merge(['reporte' => $reporteSlug], $filtros)) }}"
           class="btn btn-outline-brand" target="_blank" rel="noopener">
            <i class="bi bi-file-earmark-pdf me-1" aria-hidden="true"></i>Exportar PDF
        </a>
        <a href="{{ route('reportes.excel', array_merge(['reporte' => $reporteSlug], $filtros)) }}"
           class="btn btn-outline-brand">
            <i class="bi bi-file-earmark-excel me-1" aria-hidden="true"></i>Exportar Excel
        </a>
    </div>
</div>
