<?php

$basePath = 'C:\\Users\\Usuario\\AndroidStudioProjects\\linea61_app';

$asignacionListScreen = <<<'DART'
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/asignacion_turno_provider.dart';
import '../../providers/auth_provider.dart';
import '../../models/asignacion_turno.dart';
import '../../widgets/app_theme.dart';
import '../../widgets/app_drawer.dart';

class AsignacionTurnoListScreen extends StatefulWidget {
  const AsignacionTurnoListScreen({super.key});
  @override
  State<AsignacionTurnoListScreen> createState() => _AsignacionTurnoListScreenState();
}

class _AsignacionTurnoListScreenState extends State<AsignacionTurnoListScreen> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _loadData();
    });
  }

  void _loadData() async {
    final prov = Provider.of<AsignacionTurnoProvider>(context, listen: false);
    await prov.fetchMisAsignaciones();
    await prov.fetchMiAsignacionActual();
  }

  Color _getEstadoColor(String? estado) {
    switch (estado?.toLowerCase()) {
      case 'pendiente':
        return Colors.orange;
      case 'en_curso':
        return Colors.blue;
      case 'completado':
        return Colors.green;
      case 'retrasado':
        return Colors.deepOrange;
      case 'cancelado':
        return Colors.grey;
      default:
        return AppTheme.primary;
    }
  }

  @override
  Widget build(BuildContext context) {
    final auth = Provider.of<AuthProvider>(context);
    final isConductor = auth.user != null && auth.user!['conductor'] != null;

    return Scaffold(
      appBar: AppBar(
        title: const Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text('Control y Seguimiento de Micros', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w600, color: AppTheme.primary)),
            Text('Línea 61 · Santa Cruz', style: TextStyle(fontSize: 12, color: AppTheme.muted)),
          ],
        ),
      ),
      drawer: const AppDrawer(currentRoute: '/asignacion-turnos'),
      body: Consumer<AsignacionTurnoProvider>(
        builder: (context, prov, _) {
          return Column(
            children: [
              Padding(
                padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        const Row(
                          children: [
                            Icon(Icons.assignment, color: AppTheme.primary, size: 22),
                            SizedBox(width: 8),
                            Text('Asignación de Turnos', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w700, color: AppTheme.primary)),
                          ],
                        ),
                        const SizedBox(height: 2),
                        Text(
                          isConductor ? 'Tus turnos y unidades asignadas' : 'Operación y despacho del servicio',
                          style: const TextStyle(color: AppTheme.muted, fontSize: 13),
                        ),
                      ],
                    ),
                    IconButton(
                      icon: const Icon(Icons.refresh, color: AppTheme.primary),
                      onPressed: () => _loadData(),
                      tooltip: 'Actualizar',
                    ),
                  ],
                ),
              ),
              if (prov.error != null)
                Container(
                  margin: const EdgeInsets.all(16),
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(color: Colors.red.shade50, borderRadius: BorderRadius.circular(10), border: Border.all(color: Colors.red.shade200)),
                  child: Row(
                    children: [
                      const Icon(Icons.error_outline, color: Colors.red),
                      const SizedBox(width: 10),
                      Expanded(child: Text(prov.error!, style: const TextStyle(color: Colors.red, fontSize: 13))),
                    ],
                  ),
                ),
              const SizedBox(height: 12),
              Expanded(
                child: prov.isLoading
                    ? const Center(child: CircularProgressIndicator(color: AppTheme.primary))
                    : prov.asignaciones.isEmpty
                        ? Center(
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.center,
                              children: [
                                Icon(Icons.assignment_late_outlined, size: 54, color: AppTheme.muted.withValues(alpha: 0.35)),
                                const SizedBox(height: 12),
                                const Text('No hay asignaciones registradas.', style: TextStyle(color: AppTheme.muted, fontSize: 15, fontWeight: FontWeight.w500)),
                                const SizedBox(height: 16),
                                ElevatedButton.icon(
                                  onPressed: () => _loadData(),
                                  icon: const Icon(Icons.refresh, size: 18),
                                  label: const Text('Reintentar'),
                                  style: ElevatedButton.styleFrom(
                                    backgroundColor: AppTheme.primary,
                                    foregroundColor: Colors.white,
                                  ),
                                ),
                              ],
                            ),
                          )
                        : RefreshIndicator(
                            onRefresh: () async => _loadData(),
                            color: AppTheme.primary,
                            child: ListView.builder(
                              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
                              itemCount: prov.asignaciones.length,
                              itemBuilder: (ctx, i) {
                                final a = prov.asignaciones[i];
                                final conductorNombre = a.conductor != null 
                                    ? '${a.conductor!.nombre} ${a.conductor!.apellido}'.trim()
                                    : 'Sin conductor';
                                final microPlaca = a.micro?.placa.isNotEmpty == true ? a.micro!.placa : '1234-ABC';
                                final rutaNombre = a.ruta?.nombre.isNotEmpty == true ? a.ruta!.nombre : 'Ruta general';
                                final turnoNombre = a.turno?.displayNombre ?? 'Turno';
                                final turnoHorario = a.turno != null && a.turno!.horaInicio != null
                                    ? '${a.turno!.horaInicio} - ${a.turno!.horaFin ?? ""}'
                                    : '';
                                final estado = a.estado ?? 'pendiente';

                                return Container(
                                  margin: const EdgeInsets.only(bottom: 14),
                                  decoration: BoxDecoration(
                                    color: Colors.white,
                                    borderRadius: BorderRadius.circular(16),
                                    border: Border.all(color: AppTheme.cardBorder),
                                    boxShadow: [
                                      BoxShadow(color: Colors.black.withValues(alpha: 0.04), blurRadius: 12, offset: const Offset(0, 4)),
                                    ],
                                  ),
                                  child: Padding(
                                    padding: const EdgeInsets.all(16),
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Row(
                                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                          children: [
                                            Row(
                                              children: [
                                                Container(
                                                  padding: const EdgeInsets.all(10),
                                                  decoration: BoxDecoration(
                                                    color: AppTheme.primary.withValues(alpha: 0.08),
                                                    borderRadius: BorderRadius.circular(10),
                                                  ),
                                                  child: const Icon(Icons.directions_bus, color: AppTheme.primary, size: 22),
                                                ),
                                                const SizedBox(width: 12),
                                                Column(
                                                  crossAxisAlignment: CrossAxisAlignment.start,
                                                  children: [
                                                    Text('Placa: $microPlaca', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 16)),
                                                    Text('Fecha: ${a.fecha ?? ""}', style: const TextStyle(color: AppTheme.muted, fontSize: 13)),
                                                  ],
                                                ),
                                              ],
                                            ),
                                            Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 5),
                                              decoration: BoxDecoration(
                                                color: _getEstadoColor(estado).withValues(alpha: 0.12),
                                                borderRadius: BorderRadius.circular(20),
                                              ),
                                              child: Text(
                                                estado.toUpperCase(),
                                                style: TextStyle(
                                                  color: _getEstadoColor(estado),
                                                  fontWeight: FontWeight.w700,
                                                  fontSize: 11,
                                                  letterSpacing: 0.5,
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                        const Divider(height: 24),
                                        Row(
                                          children: [
                                            const Icon(Icons.person, size: 18, color: AppTheme.primary),
                                            const SizedBox(width: 8),
                                            Expanded(
                                              child: Text('Conductor: $conductorNombre', style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w600)),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 8),
                                        Row(
                                          children: [
                                            const Icon(Icons.alt_route, size: 18, color: AppTheme.muted),
                                            const SizedBox(width: 8),
                                            Expanded(
                                              child: Text('Ruta: $rutaNombre', style: const TextStyle(fontSize: 13, color: Colors.black87)),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 8),
                                        Row(
                                          children: [
                                            const Icon(Icons.access_time, size: 18, color: AppTheme.muted),
                                            const SizedBox(width: 8),
                                            Expanded(
                                              child: Text('Horario: $turnoNombre ($turnoHorario)', style: const TextStyle(fontSize: 13, color: Colors.black87)),
                                            ),
                                          ],
                                        ),
                                        if (estado.toLowerCase() == 'pendiente') ...[
                                          const SizedBox(height: 16),
                                          SizedBox(
                                            width: double.infinity,
                                            child: ElevatedButton.icon(
                                              style: ElevatedButton.styleFrom(
                                                backgroundColor: AppTheme.primary,
                                                foregroundColor: Colors.white,
                                                elevation: 2,
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                                                padding: const EdgeInsets.symmetric(vertical: 12),
                                              ),
                                              icon: const Icon(Icons.play_arrow, size: 22),
                                              label: const Text('Iniciar Turno', style: TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                                              onPressed: () async {
                                                final ok = await prov.iniciarTurno(a.id!);
                                                if (ok && mounted) {
                                                  ScaffoldMessenger.of(context).showSnackBar(
                                                    const SnackBar(
                                                      backgroundColor: Colors.green,
                                                      content: Text('¡Turno iniciado con éxito! Estado: EN CURSO'),
                                                    ),
                                                  );
                                                }
                                              },
                                            ),
                                          ),
                                        ],
                                      ],
                                    ),
                                  ),
                                );
                              },
                            ),
                          ),
              ),
            ],
          );
        },
      ),
    );
  }
}
DART;
file_put_contents("$basePath\\lib\\screens\\asignacion_turno\\asignacion_turno_list_screen.dart", $asignacionListScreen);

echo "Screen updated!\n";
