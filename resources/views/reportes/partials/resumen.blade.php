<div class="row g-3 mb-4 reportes-resumen">
    @foreach($resumen as $etiqueta => $valor)
        <div class="col-6 col-md-4 col-xl">
            <div class="card h-100 reportes-stat-card">
                <div class="card-body py-3">
                    <div class="meta-label reportes-stat-label">{{ $etiqueta }}</div>
                    <div class="meta-value reportes-stat-value text-brand">{{ $valor }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>
