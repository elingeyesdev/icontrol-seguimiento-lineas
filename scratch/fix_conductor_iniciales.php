<?php

$basePath = 'C:\\Users\\Usuario\\AndroidStudioProjects\\linea61_app';

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
  String get iniciales => '${nombre.isNotEmpty ? nombre[0] : ''}${apellido.isNotEmpty ? apellido[0] : ''}'.toUpperCase();
}
DART;

file_put_contents("$basePath\\lib\\models\\conductor.dart", $conductorDart);
echo "Conductor model updated with iniciales getter!\n";
