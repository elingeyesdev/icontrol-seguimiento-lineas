<?php

$basePath = 'C:\\Users\\Usuario\\AndroidStudioProjects\\linea61_app';

// 1. Update lib/models/turno.dart
$turnoModel = <<<'DART'
class Turno {
  final int? id;
  final String? nombre;
  final String? tipo;
  final String? horaInicio;
  final String? horaFin;
  final String? descripcion;
  final String? estado;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Turno({
    this.id,
    this.nombre,
    this.tipo,
    this.horaInicio,
    this.horaFin,
    this.descripcion,
    this.estado,
    this.createdAt,
    this.updatedAt,
  });

  String get displayNombre {
    if (nombre != null && nombre!.isNotEmpty) {
      return nombre![0].toUpperCase() + nombre!.substring(1);
    }
    if (tipo != null && tipo!.isNotEmpty) {
      return tipo![0].toUpperCase() + tipo!.substring(1);
    }
    return 'Turno';
  }

  factory Turno.fromJson(Map<String, dynamic> json) {
    return Turno(
      id: json['id'],
      nombre: json['nombre'] ?? json['tipo'],
      tipo: json['tipo'] ?? json['nombre'],
      horaInicio: json['hora_inicio'],
      horaFin: json['hora_fin'],
      descripcion: json['descripcion'],
      estado: json['estado'] ?? 'activo',
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at']) : null,
      updatedAt: json['updated_at'] != null ? DateTime.tryParse(json['updated_at']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'nombre': nombre ?? tipo,
      'hora_inicio': horaInicio,
      'hora_fin': horaFin,
      'descripcion': descripcion,
      'estado': estado ?? 'activo',
    };
  }
}
DART;
file_put_contents("$basePath\\lib\\models\\turno.dart", $turnoModel);

// 2. Create lib/models/asignacion_turno.dart
$asignacionModel = <<<'DART'
import 'conductor.dart';
import 'micro.dart';
import 'ruta.dart';
import 'turno.dart';

class AsignacionTurno {
  final int? id;
  final int? turnoId;
  final int? rutaId;
  final int? microId;
  final int? conductorId;
  final String? fecha;
  final String? horaSalida;
  final String? horaLlegada;
  final String? estado;
  final String? observaciones;
  final Turno? turno;
  final Conductor? conductor;
  final Micro? micro;
  final Ruta? ruta;

  AsignacionTurno({
    this.id,
    this.turnoId,
    this.rutaId,
    this.microId,
    this.conductorId,
    this.fecha,
    this.horaSalida,
    this.horaLlegada,
    this.estado,
    this.observaciones,
    this.turno,
    this.conductor,
    this.micro,
    this.ruta,
  });

  factory AsignacionTurno.fromJson(Map<String, dynamic> json) {
    return AsignacionTurno(
      id: json['id'],
      turnoId: json['turno_id'],
      rutaId: json['ruta_id'],
      microId: json['micro_id'],
      conductorId: json['conductor_id'],
      fecha: json['fecha'],
      horaSalida: json['hora_salida'],
      horaLlegada: json['hora_llegada'],
      estado: json['estado'] ?? 'pendiente',
      observaciones: json['observaciones'],
      turno: json['turno'] != null ? Turno.fromJson(json['turno']) : null,
      conductor: json['conductor'] != null ? Conductor.fromJson(json['conductor']) : null,
      micro: json['micro'] != null ? Micro.fromJson(json['micro']) : null,
      ruta: json['ruta'] != null ? Ruta.fromJson(json['ruta']) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'turno_id': turnoId,
      'ruta_id': rutaId,
      'micro_id': microId,
      'conductor_id': conductorId,
      'fecha': fecha,
      'hora_salida': horaSalida,
      'hora_llegada': horaLlegada,
      'estado': estado ?? 'pendiente',
      'observaciones': observaciones,
    };
  }
}
DART;
file_put_contents("$basePath\\lib\\models\\asignacion_turno.dart", $asignacionModel);

// 3. Create lib/providers/asignacion_turno_provider.dart
$asignacionProvider = <<<'DART'
import 'dart:convert';
import 'package:flutter/material.dart';
import '../models/asignacion_turno.dart';
import '../services/api_service.dart';

class AsignacionTurnoProvider with ChangeNotifier {
  final ApiService _api = ApiService();
  List<AsignacionTurno> _asignaciones = [];
  AsignacionTurno? _asignacionActual;
  bool _isLoading = false;
  String? _error;

  List<AsignacionTurno> get asignaciones => _asignaciones;
  AsignacionTurno? get asignacionActual => _asignacionActual;
  bool get isLoading => _isLoading;
  String? get error => _error;

  Future<void> fetchAll() async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      final response = await _api.get('/asignacion-turnos');
      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        final List data = decoded is Map && decoded.containsKey('data') ? decoded['data'] : (decoded is List ? decoded : []);
        _asignaciones = data.map((e) => AsignacionTurno.fromJson(e)).toList();
      } else {
        _error = 'Error al cargar asignaciones de turno';
      }
    } catch (e) {
      _error = 'Error de conexión: $e';
    }
    _isLoading = false;
    notifyListeners();
  }

  Future<void> fetchMisAsignaciones() async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      final response = await _api.get('/mis/asignaciones');
      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        final List data = decoded['asignaciones'] ?? [];
        _asignaciones = data.map((e) => AsignacionTurno.fromJson(e)).toList();
      }
    } catch (e) {
      _error = 'Error de conexión: $e';
    }
    _isLoading = false;
    notifyListeners();
  }

  Future<AsignacionTurno?> fetchMiAsignacionActual() async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      final response = await _api.get('/mis/asignacion-actual');
      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        if (decoded['asignacion'] != null) {
          _asignacionActual = AsignacionTurno.fromJson(decoded['asignacion']);
        } else {
          _asignacionActual = null;
        }
      }
    } catch (e) {
      _error = 'Error: $e';
    }
    _isLoading = false;
    notifyListeners();
    return _asignacionActual;
  }

  Future<bool> iniciarTurno(int asignacionId) async {
    try {
      final response = await _api.post('/mis/asignaciones/$asignacionId/iniciar', {});
      if (response.statusCode == 200) {
        await fetchMiAsignacionActual();
        await fetchAll();
        return true;
      }
    } catch (e) {
      _error = 'Error: $e';
    }
    notifyListeners();
    return false;
  }
}
DART;
file_put_contents("$basePath\\lib\\providers\\asignacion_turno_provider.dart", $asignacionProvider);

// 4. Create lib/screens/asignacion_turno/asignacion_turno_list_screen.dart
@mkdir("$basePath\\lib\\screens\\asignacion_turno", 0777, true);
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

  void _loadData() {
    final prov = Provider.of<AsignacionTurnoProvider>(context, listen: false);
    final auth = Provider.of<AuthProvider>(context, listen: false);
    
    // Si el usuario es conductor, cargamos sus asignaciones
    if (auth.user != null && auth.user!['conductor'] != null) {
      prov.fetchMisAsignaciones();
      prov.fetchMiAsignacionActual();
    } else {
      prov.fetchAll();
    }
  }

  Color _getEstadoColor(String? estado) {
    switch (estado) {
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
                                Icon(Icons.assignment_late, size: 48, color: AppTheme.muted.withValues(alpha: 0.3)),
                                const SizedBox(height: 8),
                                const Text('No hay asignaciones registradas.', style: TextStyle(color: AppTheme.muted, fontSize: 14)),
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
                                    ? '${a.conductor!.nombre ?? ""} ${a.conductor!.apellido ?? ""}'.trim()
                                    : 'Sin conductor';
                                final microPlaca = a.micro?.placa ?? 'Sin placa';
                                final rutaNombre = a.ruta?.nombre ?? 'Ruta general';
                                final turnoNombre = a.turno?.displayNombre ?? 'Turno';
                                final turnoHorario = a.turno != null && a.turno!.horaInicio != null
                                    ? '${a.turno!.horaInicio} - ${a.turno!.horaFin ?? ""}'
                                    : '';
                                final estado = a.estado ?? 'pendiente';

                                return Container(
                                  margin: const EdgeInsets.only(bottom: 12),
                                  decoration: BoxDecoration(
                                    color: Colors.white,
                                    borderRadius: BorderRadius.circular(14),
                                    border: Border.all(color: AppTheme.cardBorder),
                                    boxShadow: [
                                      BoxShadow(color: Colors.black.withValues(alpha: 0.03), blurRadius: 10, offset: const Offset(0, 4)),
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
                                                  padding: const EdgeInsets.all(8),
                                                  decoration: BoxDecoration(
                                                    color: AppTheme.primary.withValues(alpha: 0.1),
                                                    borderRadius: BorderRadius.circular(8),
                                                  ),
                                                  child: const Icon(Icons.directions_bus, color: AppTheme.primary, size: 20),
                                                ),
                                                const SizedBox(width: 10),
                                                Column(
                                                  crossAxisAlignment: CrossAxisAlignment.start,
                                                  children: [
                                                    Text('Placa: $microPlaca', style: const TextStyle(fontWeight: FontWeight.w700, fontSize: 15)),
                                                    Text('Fecha: ${a.fecha ?? ""}', style: const TextStyle(color: AppTheme.muted, fontSize: 12)),
                                                  ],
                                                ),
                                              ],
                                            ),
                                            Container(
                                              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
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
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                        const Divider(height: 20),
                                        Row(
                                          children: [
                                            const Icon(Icons.person, size: 16, color: AppTheme.muted),
                                            const SizedBox(width: 6),
                                            Expanded(
                                              child: Text('Conductor: $conductorNombre', style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w500)),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 6),
                                        Row(
                                          children: [
                                            const Icon(Icons.alt_route, size: 16, color: AppTheme.muted),
                                            const SizedBox(width: 6),
                                            Expanded(
                                              child: Text('Ruta: $rutaNombre', style: const TextStyle(fontSize: 13, color: AppTheme.muted)),
                                            ),
                                          ],
                                        ),
                                        const SizedBox(height: 6),
                                        Row(
                                          children: [
                                            const Icon(Icons.access_time, size: 16, color: AppTheme.muted),
                                            const SizedBox(width: 6),
                                            Expanded(
                                              child: Text('Turno: $turnoNombre ($turnoHorario)', style: const TextStyle(fontSize: 13, color: AppTheme.muted)),
                                            ),
                                          ],
                                        ),
                                        if (estado == 'pendiente' && isConductor) ...[
                                          const SizedBox(height: 12),
                                          SizedBox(
                                            width: double.infinity,
                                            child: ElevatedButton.icon(
                                              style: ElevatedButton.styleFrom(
                                                backgroundColor: AppTheme.primary,
                                                foregroundColor: Colors.white,
                                                shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
                                                padding: const EdgeInsets.symmetric(vertical: 10),
                                              ),
                                              icon: const Icon(Icons.play_arrow, size: 20),
                                              label: const Text('Iniciar Turno', style: TextStyle(fontWeight: FontWeight.w600)),
                                              onPressed: () async {
                                                final ok = await prov.iniciarTurno(a.id!);
                                                if (ok && mounted) {
                                                  ScaffoldMessenger.of(context).showSnackBar(
                                                    const SnackBar(content: Text('¡Turno iniciado correctamente! Buen viaje.')),
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

// 5. Update lib/main.dart
$mainFile = <<<'DART'
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

// Theme
import 'widgets/app_theme.dart';

// Providers
import 'providers/auth_provider.dart';
import 'providers/conductor_provider.dart';
import 'providers/micro_provider.dart';
import 'providers/interno_provider.dart';
import 'providers/ruta_provider.dart';
import 'providers/parada_provider.dart';
import 'providers/turno_provider.dart';
import 'providers/propietario_provider.dart';
import 'providers/asignacion_turno_provider.dart';

// Screens
import 'screens/auth/login_screen.dart';
import 'screens/dashboard/dashboard_screen.dart';
import 'screens/conductor/conductor_list_screen.dart';
import 'screens/micro/micro_list_screen.dart';
import 'screens/interno/interno_list_screen.dart';
import 'screens/ruta/ruta_list_screen.dart';
import 'screens/parada/parada_list_screen.dart';
import 'screens/turno/turno_list_screen.dart';
import 'screens/propietario/propietario_list_screen.dart';
import 'screens/asignacion_turno/asignacion_turno_list_screen.dart';

void main() {
  runApp(
    MultiProvider(
      providers: [
        ChangeNotifierProvider(create: (_) => AuthProvider()),
        ChangeNotifierProvider(create: (_) => ConductorProvider()),
        ChangeNotifierProvider(create: (_) => MicroProvider()),
        ChangeNotifierProvider(create: (_) => InternoProvider()),
        ChangeNotifierProvider(create: (_) => RutaProvider()),
        ChangeNotifierProvider(create: (_) => ParadaProvider()),
        ChangeNotifierProvider(create: (_) => TurnoProvider()),
        ChangeNotifierProvider(create: (_) => PropietarioProvider()),
        ChangeNotifierProvider(create: (_) => AsignacionTurnoProvider()),
      ],
      child: const Linea61App(),
    ),
  );
}

class Linea61App extends StatelessWidget {
  const Linea61App({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Línea 61',
      debugShowCheckedModeBanner: false,
      theme: AppTheme.lightTheme,
      home: const InitialScreen(),
      routes: {
        '/login': (context) => const LoginScreen(),
        '/dashboard': (context) => const DashboardScreen(),
        '/conductores': (context) => const ConductorListScreen(),
        '/micros': (context) => const MicroListScreen(),
        '/internos': (context) => const InternoListScreen(),
        '/rutas': (context) => const RutaListScreen(),
        '/paradas': (context) => const ParadaListScreen(),
        '/turnos': (context) => const TurnoListScreen(),
        '/propietarios': (context) => const PropietarioListScreen(),
        '/asignacion-turnos': (context) => const AsignacionTurnoListScreen(),
      },
    );
  }
}

class InitialScreen extends StatefulWidget {
  const InitialScreen({super.key});

  @override
  State<InitialScreen> createState() => _InitialScreenState();
}

class _InitialScreenState extends State<InitialScreen> {
  @override
  void initState() {
    super.initState();
    _checkAuth();
  }

  Future<void> _checkAuth() async {
    final authProvider = Provider.of<AuthProvider>(context, listen: false);
    await authProvider.checkAuthStatus();
    
    if (!mounted) return;

    if (authProvider.isAuthenticated) {
      Navigator.of(context).pushReplacementNamed('/dashboard');
    } else {
      Navigator.of(context).pushReplacementNamed('/login');
    }
  }

  @override
  Widget build(BuildContext context) {
    return const Scaffold(
      backgroundColor: AppTheme.primaryDark,
      body: Center(
        child: CircularProgressIndicator(color: Colors.white),
      ),
    );
  }
}
DART;
file_put_contents("$basePath\\lib\\main.dart", $mainFile);

// 6. Update lib/widgets/app_drawer.dart to link to /asignacion-turnos
$appDrawerContent = file_get_contents("$basePath\\lib\\widgets\\app_drawer.dart");
$appDrawerContent = str_replace(
    "_buildSubNavItem(context, icon: Icons.calendar_today_outlined, label: 'AsignaciÃ³n', route: '/turnos')",
    "_buildSubNavItem(context, icon: Icons.assignment_outlined, label: 'Asignación de Turnos', route: '/asignacion-turnos'),\n                        _buildSubNavItem(context, icon: Icons.access_time_outlined, label: 'Horarios de Turno', route: '/turnos')",
    $appDrawerContent
);
$appDrawerContent = str_replace(
    "_buildSubNavItem(context, icon: Icons.calendar_today_outlined, label: 'Asignación', route: '/turnos')",
    "_buildSubNavItem(context, icon: Icons.assignment_outlined, label: 'Asignación de Turnos', route: '/asignacion-turnos'),\n                        _buildSubNavItem(context, icon: Icons.access_time_outlined, label: 'Horarios de Turno', route: '/turnos')",
    $appDrawerContent
);
file_put_contents("$basePath\\lib\\widgets\\app_drawer.dart", $appDrawerContent);

echo "Flutter app files updated successfully!\n";
