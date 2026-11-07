<?php

$basePath = 'C:\\Users\\Usuario\\AndroidStudioProjects\\linea61_app';

$asignacionProvDart = <<<'DART'
import 'dart:convert';
import 'package:flutter/material.dart';
import '../models/asignacion_turno.dart';
import '../services/api_service.dart';

class AsignacionTurnoProvider with ChangeNotifier {
  final ApiService _api = ApiService();
  List<AsignacionTurno> _asignaciones = [];
  AsignacionTurno? _asignacionActual;
  List<dynamic> _paradasCumplidas = [];
  bool _isLoading = false;
  String? _error;

  List<AsignacionTurno> get asignaciones => _asignaciones;
  AsignacionTurno? get asignacionActual => _asignacionActual;
  List<dynamic> get paradasCumplidas => _paradasCumplidas;
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
      final response = await _api.get('/mis/asignaciones');
      if (response.statusCode == 200) {
        final decoded = jsonDecode(response.body);
        final List data = decoded['asignaciones'] ?? [];
        _asignaciones = data.map((e) => AsignacionTurno.fromJson(e as Map<String, dynamic>)).toList();
      } else {
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

  /// Envía un punto GPS al backend para seguimiento y control de paradas (SDD Sección 9 y 11)
  Future<Map<String, dynamic>?> enviarUbicacionGps({
    required int asignacionId,
    required double latitud,
    required double longitud,
    double velocidad = 0.0,
  }) async {
    try {
      final response = await _api.post('/mis/asignaciones/$asignacionId/ubicaciones', {
        'fecha_hora_gps': DateTime.now().toUtc().toIso8601String(),
        'latitud': latitud,
        'longitud': longitud,
        'velocidad': velocidad,
      });

      if (response.statusCode == 201) {
        final data = jsonDecode(response.body);
        // Si el backend culminó automáticamente el turno por GPS:
        if (data['data'] != null && data['data']['asignacion_estado'] == 'completado') {
          await fetchMiAsignacionActual();
          await fetchMisAsignaciones();
        }
        return data;
      }
    } catch (e) {
      // Silenciar en envíos periódicos de GPS
    }
    return null;
  }

  /// Sincroniza un lote de ubicaciones almacenadas offline (SDD Sección 17)
  Future<bool> sincronizarUbicacionesOffline({
    required int asignacionId,
    required List<Map<String, dynamic>> ubicacionesOffline,
  }) async {
    if (ubicacionesOffline.isEmpty) return true;

    try {
      final response = await _api.post('/mis/ubicaciones/sincronizar', {
        'asignacion_turno_id': asignacionId,
        'ubicaciones': ubicacionesOffline,
      });

      if (response.statusCode == 200) {
        await fetchMiAsignacionActual();
        return true;
      }
    } catch (e) {
      _error = 'Error al sincronizar puntos offline: $e';
    }
    return false;
  }
}
DART;

file_put_contents("$basePath\\lib\\providers\\asignacion_turno_provider.dart", $asignacionProvDart);
echo "AsignacionTurnoProvider updated with GPS ingestion methods!\n";
