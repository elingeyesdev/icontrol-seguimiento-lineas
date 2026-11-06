<?php

$basePath = 'C:\\Users\\Usuario\\AndroidStudioProjects\\linea61_app';

// 1. Bulletproof lib/models/micro.dart
$microDart = <<<'DART'
class Micro {
  final int? id;
  final int? propietarioId;
  final int? internoId;
  final String placa;
  final String? chasis;
  final dynamic anioFabricacion;
  final String modelo;
  final String marca;
  final int capacidadPasajeros;
  final String estado;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Micro({
    this.id,
    this.propietarioId,
    this.internoId,
    required this.placa,
    this.chasis,
    this.anioFabricacion,
    required this.modelo,
    required this.marca,
    required this.capacidadPasajeros,
    this.estado = 'activo',
    this.createdAt,
    this.updatedAt,
  });

  factory Micro.fromJson(Map<String, dynamic> json) {
    return Micro(
      id: json['id'] != null ? int.tryParse(json['id'].toString()) : null,
      propietarioId: json['propietario_id'] != null ? int.tryParse(json['propietario_id'].toString()) : null,
      internoId: json['interno_id'] != null ? int.tryParse(json['interno_id'].toString()) : null,
      placa: json['placa']?.toString() ?? '',
      chasis: json['chasis']?.toString(),
      anioFabricacion: json['anio_fabricacion'],
      modelo: json['modelo']?.toString() ?? '',
      marca: json['marca']?.toString() ?? '',
      capacidadPasajeros: json['capacidad_pasajeros'] != null ? (int.tryParse(json['capacidad_pasajeros'].toString()) ?? 0) : 0,
      estado: json['estado']?.toString() ?? 'activo',
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at'].toString()) : null,
      updatedAt: json['updated_at'] != null ? DateTime.tryParse(json['updated_at'].toString()) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'propietario_id': propietarioId,
      'interno_id': internoId,
      'placa': placa,
      'chasis': chasis,
      'anio_fabricacion': anioFabricacion,
      'modelo': modelo,
      'marca': marca,
      'capacidad_pasajeros': capacidadPasajeros,
      'estado': estado,
    };
  }

  String get vehiculoCompleto => '$marca $modelo';
}
DART;
file_put_contents("$basePath\\lib\\models\\micro.dart", $microDart);

// 2. Bulletproof lib/models/conductor.dart
$conductorDart = <<<'DART'
class Conductor {
  final int? id;
  final int? userId;
  final String? licencia;
  final String nombre;
  final String apellido;
  final String telefono;
  final String correo;
  final String ci;
  final String estado;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Conductor({
    this.id,
    this.userId,
    this.licencia,
    required this.nombre,
    required this.apellido,
    required this.telefono,
    required this.correo,
    required this.ci,
    this.estado = 'activo',
    this.createdAt,
    this.updatedAt,
  });

  factory Conductor.fromJson(Map<String, dynamic> json) {
    return Conductor(
      id: json['id'] != null ? int.tryParse(json['id'].toString()) : null,
      userId: json['user_id'] != null ? int.tryParse(json['user_id'].toString()) : null,
      licencia: json['licencia']?.toString(),
      nombre: json['nombre']?.toString() ?? (json['user'] != null ? json['user']['name']?.toString() ?? '' : ''),
      apellido: json['apellido']?.toString() ?? (json['user'] != null ? json['user']['apellido']?.toString() ?? '' : ''),
      telefono: json['telefono']?.toString() ?? '',
      correo: json['correo']?.toString() ?? (json['user'] != null ? json['user']['email']?.toString() ?? '' : ''),
      ci: json['ci']?.toString() ?? (json['user'] != null ? json['user']['ci']?.toString() ?? '' : ''),
      estado: json['estado']?.toString() ?? 'activo',
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at'].toString()) : null,
      updatedAt: json['updated_at'] != null ? DateTime.tryParse(json['updated_at'].toString()) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'user_id': userId,
      'licencia': licencia,
      'nombre': nombre,
      'apellido': apellido,
      'telefono': telefono,
      'correo': correo,
      'ci': ci,
      'estado': estado,
    };
  }

  String get nombreCompleto => '$nombre $apellido'.trim();
}
DART;
file_put_contents("$basePath\\lib\\models\\conductor.dart", $conductorDart);

// 3. Bulletproof lib/models/ruta.dart
$rutaDart = <<<'DART'
class Ruta {
  final int? id;
  final String nombre;
  final String? descripcion;
  final String? sentido;
  final String estado;
  final DateTime? createdAt;
  final DateTime? updatedAt;

  Ruta({
    this.id,
    required this.nombre,
    this.descripcion,
    this.sentido,
    this.estado = 'activo',
    this.createdAt,
    this.updatedAt,
  });

  factory Ruta.fromJson(Map<String, dynamic> json) {
    return Ruta(
      id: json['id'] != null ? int.tryParse(json['id'].toString()) : null,
      nombre: json['nombre']?.toString() ?? '',
      descripcion: json['descripcion']?.toString(),
      sentido: json['sentido']?.toString(),
      estado: json['estado']?.toString() ?? 'activo',
      createdAt: json['created_at'] != null ? DateTime.tryParse(json['created_at'].toString()) : null,
      updatedAt: json['updated_at'] != null ? DateTime.tryParse(json['updated_at'].toString()) : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'nombre': nombre,
      'descripcion': descripcion,
      'sentido': sentido,
      'estado': estado,
    };
  }
}
DART;
file_put_contents("$basePath\\lib\\models\\ruta.dart", $rutaDart);

// 4. Bulletproof lib/models/asignacion_turno.dart
$asignacionDart = <<<'DART'
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
      id: json['id'] != null ? int.tryParse(json['id'].toString()) : null,
      turnoId: json['turno_id'] != null ? int.tryParse(json['turno_id'].toString()) : null,
      rutaId: json['ruta_id'] != null ? int.tryParse(json['ruta_id'].toString()) : null,
      microId: json['micro_id'] != null ? int.tryParse(json['micro_id'].toString()) : null,
      conductorId: json['conductor_id'] != null ? int.tryParse(json['conductor_id'].toString()) : null,
      fecha: json['fecha']?.toString(),
      horaSalida: json['hora_salida']?.toString(),
      horaLlegada: json['hora_llegada']?.toString(),
      estado: json['estado']?.toString() ?? 'pendiente',
      observaciones: json['observaciones']?.toString(),
      turno: json['turno'] is Map<String, dynamic> ? Turno.fromJson(json['turno']) : null,
      conductor: json['conductor'] is Map<String, dynamic> ? Conductor.fromJson(json['conductor']) : null,
      micro: json['micro'] is Map<String, dynamic> ? Micro.fromJson(json['micro']) : null,
      ruta: json['ruta'] is Map<String, dynamic> ? Ruta.fromJson(json['ruta']) : null,
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
file_put_contents("$basePath\\lib\\models\\asignacion_turno.dart", $asignacionDart);

// 5. Robust lib/providers/asignacion_turno_provider.dart
$asignacionProvDart = <<<'DART'
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
        final List data = decoded is Map && decoded.containsKey('data') 
            ? decoded['data'] 
            : (decoded is List ? decoded : []);
        _asignaciones = data.map((e) => AsignacionTurno.fromJson(e as Map<String, dynamic>)).toList();
      } else {
        _error = 'Error al cargar asignaciones (${response.statusCode})';
      }
    } catch (e) {
      _error = 'Error: $e';
    }
    _isLoading = false;
    notifyListeners();
  }

  Future<void> fetchMisAsignaciones() async {
    _isLoading = true;
    _error = null;
    notifyListeners();
    try {
      // 1. Intentar /mis/asignaciones
      final response = await _api.get('/mis/asignaciones');
      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        final List data = decoded['asignaciones'] ?? [];
        _asignaciones = data.map((e) => AsignacionTurno.fromJson(e as Map<String, dynamic>)).toList();
      } else {
        // Fallback a /asignacion-turnos si no es conductor
        await fetchAll();
        return;
      }
    } catch (e) {
      _error = 'Error: $e';
    }
    _isLoading = false;
    notifyListeners();
  }

  Future<AsignacionTurno?> fetchMiAsignacionActual() async {
    try {
      final response = await _api.get('/mis/asignacion-actual');
      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        if (decoded['asignacion'] != null) {
          _asignacionActual = AsignacionTurno.fromJson(decoded['asignacion'] as Map<String, dynamic>);
        } else {
          _asignacionActual = null;
        }
      }
    } catch (e) {
      // Silenciar
    }
    notifyListeners();
    return _asignacionActual;
  }

  Future<bool> iniciarTurno(int asignacionId) async {
    try {
      final response = await _api.post('/mis/asignaciones/$asignacionId/iniciar', {});
      if (response.statusCode == 200) {
        await fetchMisAsignaciones();
        await fetchMiAsignacionActual();
        return true;
      } else {
        final data = jsonDecode(response.body);
        _error = data['message'] ?? 'No se pudo iniciar el turno';
      }
    } catch (e) {
      _error = 'Error: $e';
    }
    notifyListeners();
    return false;
  }
}
DART;
file_put_contents("$basePath\\lib\\providers\\asignacion_turno_provider.dart", $asignacionProvDart);

echo "Robust parsing models and provider written successfully!\n";
