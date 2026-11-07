<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>{{ $titulo }}</title>
    {{-- DomPDF renderiza esta vista de forma aislada: no carga el bundle de Vite ni
         admite variables CSS, por lo que la hoja del documento es autocontenida. --}}
    <style>
        * { font-family: "DejaVu Sans", Arial, sans-serif; }
        @page { margin: 26px 26px 34px; }
        body { color: #1F2937; font-size: 10.5px; margin: 0; background: #FFFFFF; }
        .encabezado { border-bottom: 2px solid #0B3C78; padding-bottom: 9px; margin-bottom: 12px; }
        .encabezado td { vertical-align: middle; }
        .logo { width: 50px; height: 50px; }
        .marca { color: #0B3C78; font-weight: bold; font-size: 15px; }
        .subtitulo { color: #7B1E2B; font-size: 10px; }
        h1 { font-size: 15px; margin: 10px 0 2px; color: #0B3C78; }
        .descripcion { color: #5B6675; font-size: 10px; margin: 0 0 10px; }
        table.meta { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.meta th { width: 20%; text-align: left; font-weight: bold; font-size: 9px; color: #5B6675; padding: 3px 6px 3px 0; }
        table.meta td { font-size: 9.5px; padding: 3px 0; }
        table.resumen { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.resumen td { border: 1px solid #E2E8F0; padding: 6px; text-align: center; background: #F7F9FC; }
        table.resumen .valor { font-size: 14px; font-weight: bold; color: #0B3C78; }
        table.resumen .etiqueta { font-size: 8.5px; color: #5B6675; }
        table.datos { width: 100%; border-collapse: collapse; }
        table.datos thead { display: table-header-group; }
        table.datos th { background: #0B3C78; color: #FFFFFF; padding: 6px 5px; font-size: 9px; text-align: left; border: 1px solid #0B3C78; }
        table.datos td { padding: 5px; border: 1px solid #E2E8F0; font-size: 9px; }
        table.datos tbody tr { page-break-inside: avoid; break-inside: avoid; }
        table.datos tbody tr:nth-child(even) td { background: #F7F9FC; }
        table.datos .sin-registros { text-align: center; color: #5B6675; padding: 14px; }
        .pie { margin-top: 14px; font-size: 8.5px; color: #5B6675; text-align: center; border-top: 1px solid #E2E8F0; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="encabezado">
        @php
            $rutaLogo = public_path('images/logo-linea61.png');
            $logo = is_file($rutaLogo)
                ? 'data:image/png;base64,'.base64_encode(file_get_contents($rutaLogo))
                : null;
        @endphp
        <table width="100%" cellpadding="0" cellspacing="0">
            <tr>
                @if($logo)
                    <td width="60"><img src="{{ $logo }}" class="logo" alt="Línea 61"></td>
                @endif
                <td>
                    <div class="marca">Línea 61 — Sistema de Control y Seguimiento de Micros</div>
                    <div class="subtitulo">Santa Cruz de la Sierra · Bolivia</div>
                </td>
            </tr>
        </table>
    </div>

    <h1>{{ $titulo }}</h1>
    <p class="descripcion">{{ $descripcion }}</p>

    <table class="meta">
        @forelse($filtrosAplicados as $etiqueta => $valor)
            <tr>
                <th>{{ $etiqueta }}</th>
                <td>{{ $valor }}</td>
            </tr>
        @empty
            <tr>
                <th>Filtros</th>
                <td>Sin filtros aplicados</td>
            </tr>
        @endforelse
        <tr>
            <th>Generado</th>
            <td>{{ $generadoEn->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <th>Registros</th>
            <td>{{ count($filas) }}</td>
        </tr>
    </table>

    @if(!empty($resumen))
        <table class="resumen">
            <tr>
                @foreach($resumen as $etiqueta => $valor)
                    <td>
                        <div class="valor">{{ $valor }}</div>
                        <div class="etiqueta">{{ $etiqueta }}</div>
                    </td>
                @endforeach
            </tr>
        </table>
    @endif

    <table class="datos">
        <thead>
            <tr>
                @foreach($encabezados as $encabezado)
                    <th>{{ $encabezado }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($filas as $fila)
                <tr>
                    @foreach($claves as $clave)
                        <td>{{ $fila[$clave] }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($encabezados) }}" class="sin-registros">
                        Sin resultados para los filtros aplicados.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="pie">Documento generado automáticamente por el sistema Línea 61.</div>
</body>
</html>
