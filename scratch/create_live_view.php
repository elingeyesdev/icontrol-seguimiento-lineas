<?php

$basePath = 'C:\\Users\\Usuario\\AndroidStudioProjects\\linea61_app';

// 1. Create lib/screens/asignacion_turno/turno_activo_screen.dart
$turnoActivoScreen = <<<'DART'
import 'dart:async';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../models/asignacion_turno.dart';
import '../../providers/asignacion_turno_provider.dart';
import '../../widgets/app_theme.dart';

class TurnoActivoScreen extends StatefulWidget {
  final AsignacionTurno asignacion;

  const TurnoActivoScreen({super.key, required this.asignacion});

  @override
  State<TurnoActivoScreen> createState() => _TurnoActivoScreenState();
}

class _TurnoActivoScreenState extends State<TurnoActivoScreen> {
  Timer? _gpsTimer;
  bool _enviandoGps = false;
  int _puntosEnviados = 0;
  String? _ultimoReporte;

  @override
  void initState() {
    super.initState();
    // Si el turno ya está en curso, simular o iniciar envío de GPS periódico
    if (widget.asignacion.estado == 'en_curso') {
      _iniciarEnvioGps();
    }
  }

  @override
  void dispose() {
    _gpsTimer?.cancel();
    super.dispose();
  }

  void _iniciarEnvioGps() {
    setState(() => _enviandoGps = true);
    // Envío periódico de GPS cada 10 segundos
    _gpsTimer = Timer.periodic(const Duration(seconds: 10), (_) {
      _reportarUbicacion();
    });
  }

  void _detenerEnvioGps() {
    _gpsTimer?.cancel();
    setState(() => _enviandoGps = false);
  }

  Future<void> _reportarUbicacion() async {
    final prov = Provider.of<AsignacionTurnoProvider>(context, listen: false);
    
    // Coordenadas de prueba basadas en la parada de la ruta
    double lat = -17.7830;
    double lng = -63.1820;
    
    final res = await prov.enviarUbicacionGps(
      asignacionId: widget.asignacion.id!,
      latitud: lat,
      longitud: lng,
      velocidad: 35.0,
    );

    if (res != null && mounted) {
      setState(() {
        _puntosEnviados++;
        _ultimoReporte = DateTime.now().toLocal().toString().substring(11, 19);
      });

      // Si el backend completó automáticamente el turno
      if (res['data'] != null && res['data']['asignacion_estado'] == 'completado') {
        _detenerEnvioGps();
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            backgroundColor: Colors.green,
            content: Text('¡Recorrido completado! El turno se cerró automáticamente.'),
          ),
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final a = widget.asignacion;
    final ruta = a.ruta;
    final paradas = ruta?.nombre != null ? [
      'Parada 1: Inicio de Ruta',
      'Parada 2: Estación Central',
      'Parada 3: Plaza Principal',
      'Parada 4: Final de Recorrido',
    ] : <String>[];

    return Scaffold(
      appBar: AppBar(
        title: const Text('Seguimiento en Vivo de Turno'),
      ),
      body: SingleChildScrollView(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            // Tarjeta de Unidad y Turno
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                gradient: AppTheme.primaryGradient,
                borderRadius: BorderRadius.circular(16),
                boxShadow: [
                  BoxShadow(color: AppTheme.primary.withValues(alpha: 0.3), blurRadius: 12, offset: const Offset(0, 4)),
                ],
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Container(
                            padding: const EdgeInsets.all(8),
                            decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.2), borderRadius: BorderRadius.circular(8)),
                            child: const Icon(Icons.directions_bus, color: Colors.white, size: 24),
                          ),
                          const SizedBox(width: 12),
                          Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(a.micro?.placa ?? 'Micro', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 18)),
                              Text('Interno: ${a.micro?.internoId ?? "A-01"}', style: TextStyle(color: Colors.white.withValues(alpha: 0.8), fontSize: 13)),
                            ],
                          ),
                        ],
                      ),
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                        decoration: BoxDecoration(color: Colors.white.withValues(alpha: 0.25), borderRadius: BorderRadius.circular(20)),
                        child: Text(
                          (a.estado ?? 'EN CURSO').toUpperCase(),
                          style: const TextStyle(color: Colors.white, fontWeight: FontWeight.bold, fontSize: 11),
                        ),
                      ),
                    ],
                  ),
                  const Divider(color: Colors.white24, height: 24),
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text('Ruta', style: TextStyle(color: Colors.white.withValues(alpha: 0.7), fontSize: 12)),
                          Text(ruta?.nombre ?? 'Línea 61', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600, fontSize: 14)),
                        ],
                      ),
                      Column(
                        crossAxisAlignment: CrossAxisAlignment.end,
                        children: [
                          Text('Turno', style: TextStyle(color: Colors.white.withValues(alpha: 0.7), fontSize: 12)),
                          Text(a.turno?.displayNombre ?? 'Mañana', style: const TextStyle(color: Colors.white, fontWeight: FontWeight.w600, fontSize: 14)),
                        ],
                      ),
                    ],
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Estado del GPS
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(16),
                border: Border.all(color: AppTheme.cardBorder),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceBetween,
                    children: [
                      Row(
                        children: [
                          Icon(
                            _enviandoGps ? Icons.gps_fixed : Icons.gps_not_fixed,
                            color: _enviandoGps ? Colors.green : Colors.grey,
                            size: 20,
                          ),
                          const SizedBox(width: 8),
                          Text(
                            _enviandoGps ? 'Transmisión GPS Activa' : 'GPS en Pausa',
                            style: TextStyle(
                              fontWeight: FontWeight.w700,
                              color: _enviandoGps ? Colors.green.shade700 : Colors.grey.shade700,
                              fontSize: 14,
                            ),
                          ),
                        ],
                      ),
                      Text('Puntos: $_puntosEnviados', style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w600)),
                    ],
                  ),
                  if (_ultimoReporte != null) ...[
                    const SizedBox(height: 6),
                    Text('Última transmisión: $_ultimoReporte', style: const TextStyle(fontSize: 12, color: AppTheme.muted)),
                  ],
                  const SizedBox(height: 12),
                  SizedBox(
                    width: double.infinity,
                    child: OutlinedButton.icon(
                      onPressed: () => _reportarUbicacion(),
                      icon: const Icon(Icons.send_rounded, size: 18),
                      label: const Text('Enviar Posición GPS Ahora'),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Secuencia de Paradas del Recorrido
            const Text('Recorrido y Paradas', style: TextStyle(fontWeight: FontWeight.bold, fontSize: 16, color: AppTheme.primary)),
            const SizedBox(height: 8),
            Text('El sistema detectará automáticamente cada parada cumplida por GPS.', style: TextStyle(color: AppTheme.muted.withValues(alpha: 0.8), fontSize: 13)),
            const SizedBox(height: 12),

            ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: paradas.length,
              itemBuilder: (ctx, i) {
                final isLast = i == paradas.length - 1;
                return Container(
                  margin: const EdgeInsets.only(bottom: 8),
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: AppTheme.cardBorder),
                  ),
                  child: Row(
                    children: [
                      CircleAvatar(
                        radius: 14,
                        backgroundColor: isLast ? Colors.red.shade100 : AppTheme.primary.withValues(alpha: 0.1),
                        child: Text(
                          '${i + 1}',
                          style: TextStyle(
                            fontSize: 12,
                            fontWeight: FontWeight.bold,
                            color: isLast ? Colors.red.shade800 : AppTheme.primary,
                          ),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          paradas[i],
                          style: TextStyle(
                            fontSize: 14,
                            fontWeight: isLast ? FontWeight.w700 : FontWeight.w500,
                          ),
                        ),
                      ),
                      if (isLast)
                        Container(
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                          decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(10), border: Border.all(color: Colors.green.shade200)),
                          child: const Text('Cierre Automático', style: TextStyle(fontSize: 10, color: Colors.green, fontWeight: FontWeight.bold)),
                        ),
                    ],
                  ),
                );
              },
            ),
          ],
        ),
      ),
    );
  }
}
DART;

file_put_contents("$basePath\\lib\\screens\\asignacion_turno\\turno_activo_screen.dart", $turnoActivoScreen);

// 2. Update asignacion_turno_list_screen.dart to open TurnoActivoScreen on tap or when started
$listScreen = file_get_contents("$basePath\\lib\\screens\\asignacion_turno\\asignacion_turno_list_screen.dart");
if (! str_contains($listScreen, 'turno_activo_screen.dart')) {
    $listScreen = "import 'turno_activo_screen.dart';\n".$listScreen;
    $listScreen = str_replace(
        'onTap: () async {',
        "onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => TurnoActivoScreen(asignacion: a))),\n                                              child: const SizedBox(),\n                                            );\n                                          },\n                                        ),\n                                        onTap2: () async {",
        $listScreen
    );
    file_put_contents("$basePath\\lib\\screens\\asignacion_turno\\asignacion_turno_list_screen.dart", $listScreen);
}

// 3. Make card clickable in asignacion_turno_list_screen.dart
$screenUpdated = <<<'DART'
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';
import '../../providers/asignacion_turno_provider.dart';
import '../../providers/auth_provider.dart';
import '../../models/asignacion_turno.dart';
import '../../widgets/app_theme.dart';
import '../../widgets/app_drawer.dart';
import 'turno_activo_screen.dart';

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
                                  child: Material(
                                    color: Colors.transparent,
                                    child: InkWell(
                                      borderRadius: BorderRadius.circular(16),
                                      onTap: () {
                                        Navigator.push(
                                          context,
                                          MaterialPageRoute(builder: (_) => TurnoActivoScreen(asignacion: a)),
                                        );
                                      },
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
                                                      Navigator.push(
                                                        context,
                                                        MaterialPageRoute(builder: (_) => TurnoActivoScreen(asignacion: a)),
                                                      );
                                                    }
                                                  },
                                                ),
                                              ),
                                            ],
                                          ],
                                        ),
                                      ),
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

file_put_contents("$basePath\\lib\\screens\\asignacion_turno\\asignacion_turno_list_screen.dart", $screenUpdated);
echo "Live tracking view created and linked successfully!\n";
