<?php

namespace App\Http\Controllers;

use App\Reportes\ReporteAsignacionesPorConductor;
use App\Reportes\ReporteBase;
use App\Reportes\ReporteMicrosPorPropietario;
use App\Reportes\ReporteParadasOmitidas;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Módulo de Reportes (SDD — listados de consulta y exportación).
 *
 * Cada reporte concreto vive en App\Reportes y expone su propia consulta.
 * Este controlador únicamente resuelve filtros, presenta en pantalla y
 * genera las exportaciones PDF (DomPDF) y Excel (CSV).
 */
class ReporteController extends Controller
{
    /**
     * Catálogo de reportes disponibles: slug => implementación.
     *
     * @var array<string, class-string<ReporteBase>>
     */
    private const REPORTES = [
        'asignaciones-conductor' => ReporteAsignacionesPorConductor::class,
        'micros-propietario' => ReporteMicrosPorPropietario::class,
        'paradas-omitidas' => ReporteParadasOmitidas::class,
    ];

    /**
     * Panel de acceso a los reportes.
     */
    public function index(): View
    {
        $reportes = collect(self::REPORTES)->map(function (string $clase, string $slug) {
            /** @var ReporteBase $reporte */
            $reporte = app($clase);

            return [
                'slug' => $slug,
                'titulo' => $reporte->titulo(),
                'descripcion' => $reporte->descripcion(),
            ];
        })->values();

        return view('reportes.index', compact('reportes'));
    }

    /**
     * Listado en pantalla del reporte solicitado.
     */
    public function show(Request $request, string $reporte): View
    {
        $datos = $this->construir($reporte, $request);

        return view("reportes.{$reporte}", [
            'reporteSlug' => $reporte,
            'titulo' => $datos['reporte']->titulo(),
            'descripcion' => $datos['reporte']->descripcion(),
            'columnas' => $datos['reporte']->columnas(),
            'claves' => $datos['reporte']->claves(),
            'filas' => $datos['pagina'],
            'resumen' => $datos['resumen'],
            'filtros' => $datos['filtros'],
            'filtrosAplicados' => $datos['filtrosAplicados'],
            'opciones' => $datos['reporte']->opcionesFiltros(),
        ]);
    }

    /**
     * Exporta el reporte a PDF con DomPDF.
     */
    public function pdf(Request $request, string $reporte): Response
    {
        $datos = $this->construir($reporte, $request);

        $pdf = Pdf::loadView('reportes.documento', [
            'titulo' => $datos['reporte']->titulo(),
            'descripcion' => $datos['reporte']->descripcion(),
            'encabezados' => $datos['reporte']->encabezados(),
            'claves' => $datos['reporte']->claves(),
            'filas' => $datos['filas'],
            'resumen' => $datos['resumen'],
            'filtrosAplicados' => $datos['filtrosAplicados'],
            'generadoEn' => now(),
            'modo' => 'pdf',
        ])->setPaper('a4', 'landscape');

        return $pdf->download(Str::slug($datos['reporte']->titulo()).'.pdf');
    }

    /**
     * Exporta el reporte a Excel mediante CSV compatible (UTF-8 con BOM).
     */
    public function excel(Request $request, string $reporte): StreamedResponse
    {
        $datos = $this->construir($reporte, $request);
        $claves = $datos['reporte']->claves();
        $encabezados = $datos['reporte']->encabezados();
        $filas = $datos['filas'];
        $nombre = Str::slug($datos['reporte']->titulo()).'.csv';

        return response()->streamDownload(function () use ($claves, $encabezados, $filas) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");
            fputcsv($salida, $encabezados, ';');
            foreach ($filas as $fila) {
                fputcsv($salida, array_map(fn ($clave) => $fila[$clave] ?? '', $claves), ';');
            }
            fclose($salida);
        }, $nombre, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Resuelve el reporte y ejecuta su consulta con los filtros recibidos.
     *
     * @return array{reporte: ReporteBase, filtros: array<string, mixed>, filas: Collection<int, array<string, string>>, resumen: array<string, int|string>, filtrosAplicados: array<string, string>, pagina: LengthAwarePaginator}
     */
    private function construir(string $reporte, Request $request): array
    {
        abort_unless(array_key_exists($reporte, self::REPORTES), 404);

        /** @var ReporteBase $instancia */
        $instancia = app(self::REPORTES[$reporte]);
        $filtros = $request->except(['page', '_token']);

        $filas = $instancia->resultados($filtros);

        return [
            'reporte' => $instancia,
            'filtros' => $filtros,
            'filas' => $filas,
            'resumen' => $instancia->resumen($filas),
            'filtrosAplicados' => $instancia->filtrosAplicados($filtros),
            'pagina' => $instancia->paginar($filas),
        ];
    }
}
