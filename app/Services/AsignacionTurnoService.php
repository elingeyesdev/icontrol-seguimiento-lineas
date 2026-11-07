<?php

namespace App\Services;

use App\Models\AsignacionTurno;
use App\Models\Conductor;
use App\Models\Micro;
use App\Models\Ruta;
use App\Models\Turno;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AsignacionTurnoService
{
    public function __construct(
        protected ControlRecorridoService $controlRecorridoService
    ) {}

    /**
     * Crea una nueva asignación de turno validando reglas de negocio.
     */
    public function crear(array $datos): AsignacionTurno
    {
        $this->validarTurnoActivo($datos['turno_id']);
        $this->validarRecursosActivos($datos);
        $this->validarDisponibilidad($datos);

        $datos['estado'] = 'pendiente';
        unset($datos['hora_salida'], $datos['hora_llegada']);

        return AsignacionTurno::create($datos);
    }

    /**
     * Actualiza una asignación de turno existente.
     */
    public function actualizar(AsignacionTurno $asignacion, array $datos): AsignacionTurno
    {
        $this->validarTurnoActivo($datos['turno_id']);
        $this->validarDisponibilidad($datos, $asignacion->id);

        $asignacion->update($datos);

        return $asignacion;
    }

    /**
     * Inicia una asignación de turno (pendiente -> en_curso).
     */
    public function iniciar(AsignacionTurno $asignacion, ?int $conductorId = null): AsignacionTurno
    {
        return DB::transaction(function () use ($asignacion, $conductorId) {
            // Bloquear la fila evita dos inicios simultáneos de la misma asignación.
            $asignacion = AsignacionTurno::query()
                ->lockForUpdate()
                ->findOrFail($asignacion->id);

            if ($conductorId !== null && (int) $asignacion->conductor_id !== (int) $conductorId) {
                throw ValidationException::withMessages([
                    'conductor' => 'No tienes autorización para iniciar esta asignación de turno.',
                ]);
            }

            if ($asignacion->estado !== 'pendiente') {
                throw ValidationException::withMessages([
                    'estado' => "La asignación no puede iniciarse porque su estado actual es '{$asignacion->estado}'.",
                ]);
            }

            $asignacion->update([
                'estado' => 'en_curso',
                'hora_salida' => now()->format('H:i:s'),
            ]);

            // El control de recorrido toma las paradas de la ruta al iniciar el turno.
            $this->controlRecorridoService->planificar($asignacion);

            return $asignacion->fresh(['turno', 'conductor', 'micro.interno', 'ruta.paradas']);
        });
    }

    /**
     * Finaliza una asignación de turno (en_curso/retrasado -> completado).
     */
    public function finalizar(AsignacionTurno $asignacion, ?int $conductorId = null, ?string $observaciones = null): AsignacionTurno
    {
        if ($conductorId !== null && (int) $asignacion->conductor_id !== (int) $conductorId) {
            throw ValidationException::withMessages([
                'conductor' => 'No tienes autorización para finalizar esta asignación de turno.',
            ]);
        }

        if (! in_array($asignacion->estado, ['en_curso', 'retrasado'])) {
            throw ValidationException::withMessages([
                'estado' => "La asignación no puede finalizarse porque su estado actual es '{$asignacion->estado}'.",
            ]);
        }

        $cambios = [
            'estado' => 'completado',
            'hora_llegada' => now()->format('H:i:s'),
        ];

        // Antes el cuerpo de la petición se ignoraba por completo y las
        // observaciones enviadas por el conductor se perdian en silencio.
        if ($observaciones !== null && trim($observaciones) !== '') {
            $cambios['observaciones'] = $observaciones;
        }

        $asignacion->update($cambios);

        return $asignacion->fresh(['turno', 'conductor', 'micro.interno', 'ruta.paradas']);
    }

    /**
     * Cancela lógicamente una asignación de turno.
     */
    public function cancelar(AsignacionTurno $asignacion): AsignacionTurno
    {
        $asignacion->update(['estado' => 'cancelado']);

        return $asignacion;
    }

    /**
     * Valida que el turno seleccionado se encuentre activo.
     */
    protected function validarTurnoActivo(int $turnoId): void
    {
        $turno = Turno::findOrFail($turnoId);

        if ($turno->estado !== 'activo') {
            throw ValidationException::withMessages([
                'turno_id' => 'El turno seleccionado está inactivo.',
            ]);
        }
    }

    /**
     * Valida que los recursos seleccionados para una nueva asignación
     * estén activos. Las asignaciones históricas conservan sus referencias,
     * pero una asignación operativa nueva no debe apuntar a un recurso
     * desactivado.
     *
     * @param  array<string, mixed>  $datos
     */
    protected function validarRecursosActivos(array $datos): void
    {
        $micro = Micro::find($datos['micro_id'] ?? null);

        if (! $micro || $micro->estado !== 'activo') {
            throw ValidationException::withMessages([
                'micro_id' => 'El micro seleccionado no está activo o no existe.',
            ]);
        }

        $conductor = Conductor::find($datos['conductor_id'] ?? null);

        if (! $conductor || $conductor->estado !== 'activo') {
            throw ValidationException::withMessages([
                'conductor_id' => 'El conductor seleccionado no está activo o no existe.',
            ]);
        }

        $ruta = Ruta::find($datos['ruta_id'] ?? null);

        if (! $ruta || $ruta->estado !== 'activo') {
            throw ValidationException::withMessages([
                'ruta_id' => 'La ruta seleccionada no está activa o no existe.',
            ]);
        }
    }

    /**
     * Valida que el conductor o micro no estén duplicados en la misma fecha y turno.
     */
    protected function validarDisponibilidad(array $datos, ?int $exceptId = null): void
    {
        // 1. Validar conductor en fecha y turno (si la asignación no está cancelada)
        $conductorOcupado = AsignacionTurno::where('conductor_id', $datos['conductor_id'])
            ->where('fecha', $datos['fecha'])
            ->where('turno_id', $datos['turno_id'])
            ->where('estado', '!=', 'cancelado')
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists();

        if ($conductorOcupado) {
            throw ValidationException::withMessages([
                'conductor_id' => 'El conductor seleccionado ya tiene una asignación activa para este turno y fecha.',
            ]);
        }

        // 2. Validar micro en fecha y turno
        $microOcupado = AsignacionTurno::where('micro_id', $datos['micro_id'])
            ->where('fecha', $datos['fecha'])
            ->where('turno_id', $datos['turno_id'])
            ->where('estado', '!=', 'cancelado')
            ->when($exceptId, fn ($q) => $q->where('id', '!=', $exceptId))
            ->exists();

        if ($microOcupado) {
            throw ValidationException::withMessages([
                'micro_id' => 'El micro seleccionado ya se encuentra asignado a este turno y fecha.',
            ]);
        }
    }
}
