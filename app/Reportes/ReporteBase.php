<?php

namespace App\Reportes;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginador;
use Illuminate\Support\Collection;

/**
 * Contrato y utilidades compartidas por los reportes del sistema.
 *
 * Cada reporte concreta su consulta en `resultados()` y define las columnas
 * visibles en `columnas()`. La presentación (web, PDF, Excel) se genera a
 * partir de estas dos definiciones, de modo que siempre exista una única
 * fuente de verdad para los datos.
 */
abstract class ReporteBase
{
    /**
     * Título legible del reporte.
     */
    abstract public function titulo(): string;

    /**
     * Descripción breve mostrada bajo el título.
     */
    abstract public function descripcion(): string;

    /**
     * Columnas del reporte expresadas como [clave => etiqueta].
     *
     * @return array<string, string>
     */
    abstract public function columnas(): array;

    /**
     * Ejecuta la consulta y devuelve las filas ya formateadas para presentación.
     *
     * @param  array<string, mixed>  $filtros
     * @return Collection<int, array<string, string>>
     */
    abstract public function resultados(array $filtros): Collection;

    /**
     * Totales y métricas derivadas de las filas del reporte.
     *
     * @param  Collection<int, array<string, string>>  $filas
     * @return array<string, int|string>
     */
    public function resumen(Collection $filas): array
    {
        return ['Total de registros' => $filas->count()];
    }

    /**
     * Filtros normalizados y legibles usados en el encabezado y en el PDF.
     *
     * @param  array<string, mixed>  $filtros
     * @return array<string, string>
     */
    public function filtrosAplicados(array $filtros): array
    {
        return collect($filtros)
            ->reject(fn ($valor) => $valor === null || $valor === '')
            ->map(fn ($valor) => (string) $valor)
            ->all();
    }

    /**
     * Fuentes de datos para poblar los selectores de filtros de la vista.
     *
     * @return array<string, Collection<int, mixed>|array<int, mixed>>
     */
    public function opcionesFiltros(): array
    {
        return [];
    }

    /**
     * Claves de las columnas en el orden definido.
     *
     * @return array<int, string>
     */
    public function claves(): array
    {
        return array_keys($this->columnas());
    }

    /**
     * Etiquetas de las columnas en el orden definido.
     *
     * @return array<int, string>
     */
    public function encabezados(): array
    {
        return array_values($this->columnas());
    }

    /**
     * Pagina una colección de filas manteniendo los parámetros de la petición.
     *
     * @param  Collection<int, array<string, string>>  $filas
     */
    public function paginar(Collection $filas, int $porPagina = 15): LengthAwarePaginator
    {
        $pagina = Paginador::resolveCurrentPage('page');
        $items = $filas->forPage($pagina, $porPagina)->values();

        return new Paginador($items, $filas->count(), $porPagina, $pagina, [
            'path' => Paginador::resolveCurrentPath(),
        ]);
    }
}
