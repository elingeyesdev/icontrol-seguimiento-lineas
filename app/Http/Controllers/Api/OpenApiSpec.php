<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'API Linea 61 Control',
    description: 'API de Control y Seguimiento de Micros - Línea 61'
)]
#[OA\SecurityScheme(
    securityScheme: 'sanctum',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
#[OA\Post(
    path: '/api/login',
    summary: 'Inicio de sesión API',
    responses: [
        new OA\Response(response: 200, description: 'Login exitoso'),
    ]
)]
#[OA\Get(
    path: '/api/conductores',
    summary: 'Listado de conductores',
    security: [['sanctum' => []]],
    responses: [
        new OA\Response(response: 200, description: 'Lista de conductores'),
    ]
)]
class OpenApiSpec {}
