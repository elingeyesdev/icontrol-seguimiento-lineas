<?php

namespace App\Services;

use App\Models\AsignacionTurno;
use App\Models\Conductor;
use App\Models\Micro;
use App\Models\Ruta;
use App\Models\Turno;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Protege la integridad de las asignaciones de turno frente a la
 * desactivación de los catálogos que las referencian.
 *
 * Regla de negocio: un conductor, micro, ruta o turno con asignaciones
 * pendientes, en curso o retrasadas no puede desactivarse, porque dejaría
 * una asignación operativa apuntando a un recurso fuera de servicio.
 * Las asignaciones completadas o canceladas son historial y no bloquean
 * la desactivación lógica del catálogo.
 */
class IntegridadAsignacionesService
{
    /**
     * Estados de una asignación que todavía afectan la operación.
     *
     * @var list<string>
     */
    public const ESTADOS_OPERATIVOS = ['pendiente', 'en_curso', 'retrasado'];

    /**
     * Referencias directas de `asignacion_turnos` a los catálogos.
     *
     * @var array<class-string<Model>, array{columna: string, etiqueta: string, articulo: string}>
     */
    private const REFERENCIAS = [
        Conductor::class => ['columna' => 'conductor_id', 'etiqueta' => 'conductor', 'articulo' => 'el'],
        Micro::class => ['columna' => 'micro_id', 'etiqueta' => 'micro', 'articulo' => 'el'],
        Ruta::class => ['columna' => 'ruta_id', 'etiqueta' => 'ruta', 'articulo' => 'la'],
        Turno::class => ['columna' => 'turno_id', 'etiqueta' => 'turno', 'articulo' => 'el'],
    ];

    /**
     * Impide la desactivación de una entidad referenciada por asignaciones
     * que siguen vigentes en la operación.
     *
     * @throws ValidationException
     */
    public function asegurarDesactivacionPermitida(Model $entidad): void
    {
        $referencia = self::REFERENCIAS[$entidad::class] ?? null;

        if ($referencia === null) {
            return;
        }

        $total = $this->asignacionesOperativas($entidad, $referencia['columna']);

        if ($total === 0) {
            return;
        }

        throw ValidationException::withMessages([
            'estado' => sprintf(
                'No se puede desactivar %s %s %s porque tiene %d asignación(es) de turno pendiente(s), en curso o retrasada(s). Finaliza o cancela esas asignaciones antes de desactivarla.',
                $referencia['articulo'],
                $referencia['etiqueta'],
                $this->identificar($entidad),
                $total,
            ),
        ]);
    }

    /**
     * Valida el cambio de estado cuando una actualización intenta pasar el
     * registro a inactivo (por ejemplo, desde el formulario de edición).
     *
     * @param  array<string, mixed>  $datos
     *
     * @throws ValidationException
     */
    public function asegurarCambioDeEstadoPermitido(Model $entidad, array $datos): void
    {
        if (($datos['estado'] ?? null) !== 'inactivo') {
            return;
        }

        if ($entidad->getAttribute('estado') === 'inactivo') {
            return;
        }

        $this->asegurarDesactivacionPermitida($entidad);
    }

    /**
     * Desactiva lógicamente la entidad dentro de una transacción, bloqueando
     * su fila para que no se cree una asignación operativa en paralelo.
     */
    public function desactivar(Model $entidad): Model
    {
        return DB::transaction(function () use ($entidad) {
            $bloqueada = $entidad->newQuery()->lockForUpdate()->findOrFail($entidad->getKey());

            $this->asegurarDesactivacionPermitida($bloqueada);

            if ($bloqueada->getAttribute('estado') !== 'inactivo') {
                $bloqueada->update(['estado' => 'inactivo']);
            }

            return $bloqueada;
        });
    }

    /**
     * Cantidad de asignaciones operativas que referencian a la entidad.
     */
    public function asignacionesOperativas(Model $entidad, string $columna): int
    {
        return AsignacionTurno::query()
            ->where($columna, $entidad->getKey())
            ->whereIn('estado', self::ESTADOS_OPERATIVOS)
            ->count();
    }

    /**
     * Identificación legible de la entidad para el mensaje de bloqueo.
     */
    private function identificar(Model $entidad): string
    {
        return match (true) {
            $entidad instanceof Conductor => $entidad->nombreCompleto(),
            $entidad instanceof Micro => 'con placa '.$entidad->placa,
            default => (string) $entidad->getAttribute('nombre'),
        };
    }
}
