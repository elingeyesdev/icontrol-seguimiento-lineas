# Módulo de Reportes — Línea 61

Documento de cierre de la tarea de integración del **módulo de Reportes** al proyecto.
Describe qué se intervino, cómo quedó integrado y cómo usarlo.

---

## 1. Objetivo

Integrar el módulo de Reportes con los siguientes listados:

1. **Listar Asignaciones de Turnos por Conductor (mes y año).**
2. **Listar Micros por Propietario e Interno asignado.**
3. **Listar Paradas Omitidas por Ruta y Conductor.**

Cada reporte se presenta en pantalla (con filtros y paginación) y permite **Imprimir**, **Exportar a PDF** y **Exportar a Excel**.

---

## 2. Arquitectura de la solución

Se respetó la arquitectura Laravel y las convenciones del proyecto. La **lógica de consulta vive en una carpeta dedicada** (`app/Reportes`), separada de controladores y vistas.

```
app/
├── Http/Controllers/ReporteController.php   # Resuelve filtros, presenta y exporta
└── Reportes/                                # ← Lógica/consultas del módulo
    ├── ReporteBase.php
    ├── ReporteAsignacionesPorConductor.php
    ├── ReporteMicrosPorPropietario.php
    └── ReporteParadasOmitidas.php

resources/
├── css/reportes.css
└── views/reportes/
    ├── index.blade.php                      # Panel de reportes
    ├── asignaciones-conductor.blade.php     # Vista del reporte 1
    ├── micros-propietario.blade.php         # Vista del reporte 2
    ├── paradas-omitidas.blade.php           # Vista del reporte 3
    ├── documento.blade.php                  # Plantilla del PDF
    └── partials/
        ├── encabezado.blade.php             # Título + botones (imprimir/pdf/excel)
        ├── resumen.blade.php                # Tarjetas de totales
        └── tabla.blade.php                  # Tabla genérica + badges
```

### Patrón de diseño

- `ReporteBase` define el contrato común: `titulo()`, `descripcion()`, `columnas()`, `resultados()`, `resumen()`, `filtrosAplicados()`, `opcionesFiltros()` y `paginar()`.
- Cada reporte concreto solo implementa **su consulta** y su formato de filas. La presentación, el PDF y el Excel se generan a partir de las mismas definiciones (`columnas` + `resultados`), evitando duplicar lógica.
- `ReporteController` es genérico: resuelve el reporte por *slug*, ejecuta la consulta y genera las salidas (web / PDF / Excel).

---

## 3. Archivos creados

| Archivo | Descripción |
|---|---|
| `app/Reportes/ReporteBase.php` | Clase base abstracta de los reportes. |
| `app/Reportes/ReporteAsignacionesPorConductor.php` | Consulta del reporte de asignaciones por conductor. |
| `app/Reportes/ReporteMicrosPorPropietario.php` | Consulta del reporte de micros por propietario e interno. |
| `app/Reportes/ReporteParadasOmitidas.php` | Consulta del reporte de paradas omitidas. |
| `app/Http/Controllers/ReporteController.php` | Controlador del módulo. |
| `resources/views/reportes/index.blade.php` | Panel de acceso a los reportes. |
| `resources/views/reportes/asignaciones-conductor.blade.php` | Listado web del reporte 1. |
| `resources/views/reportes/micros-propietario.blade.php` | Listado web del reporte 2. |
| `resources/views/reportes/paradas-omitidas.blade.php` | Listado web del reporte 3. |
| `resources/views/reportes/documento.blade.php` | Plantilla HTML que DomPDF convierte a PDF. |
| `resources/views/reportes/partials/*.blade.php` | Parciales reutilizables (encabezado, resumen, tabla). |
| `resources/css/reportes.css` | Estilos del módulo (incluye estilos de impresión). |
| `public/images/logo-linea61.png` | Logotipo institucional usado en el encabezado web y en el PDF. |
| `tests/Feature/ReportesTest.php` | Pruebas de render, filtros, PDF, Excel y 404. |
| `REPORTES.md` | Este documento. |

## 4. Archivos modificados

| Archivo | Cambio |
|---|---|
| `routes/web.php` | Se agregó el grupo de rutas `reportes.*` (protegido por roles `admin`, `propietario`, `fiscalizador`). |
| `resources/views/layouts/navigation.blade.php` | Se reemplazaron los enlaces vacíos (`#`) del submenú **Reportes** por los 3 reportes reales, con estado activo. |
| `vite.config.js` | Se registró `resources/css/reportes.css` en la lista de entrada de Vite. |
| `composer.json` / `composer.lock` | Se agregó la dependencia **`barryvdh/laravel-dompdf`** (`^3.1`) para generar PDF. |

---

## 5. Rutas

Todas bajo autenticación + rol (`admin | propietario | fiscalizador`):

| Método | URI | Nombre | Acción |
|---|---|---|---|
| GET | `/reportes` | `reportes.index` | Panel de reportes |
| GET | `/reportes/{reporte}` | `reportes.show` | Listado en pantalla |
| GET | `/reportes/{reporte}/pdf` | `reportes.pdf` | Descarga PDF |
| GET | `/reportes/{reporte}/excel` | `reportes.excel` | Descarga Excel (CSV) |

*Slugs* válidos para `{reporte}`: `asignaciones-conductor`, `micros-propietario`, `paradas-omitidas`.

---

## 6. Detalle de cada reporte

### 6.1 Asignaciones de Turnos por Conductor (mes y año)
- **Filtros:** Mes, Año, Conductor, Estado.
- **Columnas:** Código, Fecha, Turno, Conductor, Ruta, Micro / Interno, Hora salida, Hora llegada, Estado.
- **Resumen:** Total de asignaciones, completadas, en curso/pendientes, canceladas y conductores.
- **Consulta:** `asignacion_turnos` con `JOIN` a conductor, ruta, micro, interno y turno; filtra por mes/año con `whereYear`/`whereMonth`.

### 6.2 Micros por Propietario e Interno Asignado
- **Filtros:** Propietario, Interno asignado, Estado, Búsqueda (placa/marca/modelo/interno/propietario).
- **Columnas:** Placa, Vehículo, Propietario, Interno asignado, Capacidad, Estado.
- **Resumen:** Total de micros, activos, inactivos, con interno asignado y propietarios.
- **Consulta:** `micro` con `JOIN` a `propietarios`, `users` (datos actualizados del usuario) e `interno`.

### 6.3 Paradas Omitidas por Ruta y Conductor
- **Filtros:** Ruta, Conductor, Desde, Hasta.
- **Columnas:** Fecha, Hora, Turno, Conductor, Ruta, Micro / Interno, Parada omitida, Sentido, Orden, Origen.
- **Resumen:** Total de paradas omitidas, rutas afectadas, conductores involucrados y paradas distintas.
- **Definición de "parada omitida"** (dado que el servicio de control solo escribe estado `cumplido`):
  1. **Registrado:** registros de `control_recorrido` con estado `omitido` (marcados explícitamente).
  2. **Derivado:** en recorridos **finalizados** (`asignacion_turnos.estado = 'completado'`), toda parada activa de la ruta que **no** tiene un control `cumplido`.
  - La columna **Origen** indica cuál de los dos criterios aplica, y los registros explícitos tienen prioridad al fusionar.

---

## 7. Exportaciones

- **Imprimir:** botón que ejecuta `window.print()` sobre el listado. El CSS oculta el menú lateral, la barra superior, los filtros y la paginación para una impresión limpia.
- **Exportar PDF:** generado con **DomPDF** a partir de `resources/views/reportes/documento.blade.php` (A4 horizontal, encabezado institucional con **logotipo**, resumen y tabla). El logotipo se incrusta como base64 para que el PDF sea autocontenido y no dependa de rutas del servidor.
- **Exportar Excel:** descarga **CSV UTF‑8 con BOM** y separador `;`, compatible con Excel (abre correctamente acentos y columnas).

> **Nota sobre Excel (.xlsx):** no se pudo instalar `maatwebsite/excel` porque el entorno **no tiene la extensión `ext-zip`** de PHP y existen incompatibilidades con Laravel 13. Por eso la exportación a Excel se implementó como CSV nativo, sin dependencias extra. Si en el futuro se habilita `ext-zip`, se puede incorporar un exportador `.xlsx` real.

> **Nota sobre tipografía en PDF:** las vistas web usan **Inter** (igual que el resto del sistema). DomPDF no descarga fuentes externas, por lo que el PDF usa **DejaVu Sans**, la sans-serif disponible más cercana.

---

## 8. Estilos

- Se creó `resources/css/reportes.css`, respetando la paleta y las variables existentes (`--primary`, `--accent`, `--card-shadow`, etc.) y la tipografía global del sistema.
- Se reutilizaron los patrones de tarjetas, encabezados y tablas de los módulos ya existentes (por ejemplo, `micro` y `control-paradas`) para mantener uniformidad visual.
- El **logotipo** (`public/images/logo-linea61.png`) se muestra en el encabezado de cada reporte y en el PDF, con el mismo tratamiento de esquinas redondeadas y sombra que las tarjetas del sistema.

---

## 9. Pruebas

Archivo: `tests/Feature/ReportesTest.php` (8 pruebas / 24 aserciones). Cubre:

- Render del panel de reportes.
- Render de los tres reportes y su contenido esperado.
- Filtro por mes/año (excluye registros de otro periodo).
- Exportación **PDF** (cabecera `application/pdf`).
- Exportación **Excel/CSV** (cabecera `text/csv`).
- Reporte inexistente devuelve **404**.

Resultado:
- `tests/Feature/ReportesTest.php` → **8 passed**.
- Suite completa → **64 passed (203 assertions)**, sin regresiones.

---

## 10. Instalación / pasos aplicados en el entorno

1. `composer require barryvdh/laravel-dompdf` (instaló DomPDF 3.1.6).
2. `composer dump-autoload` + `php artisan package:discover`.
3. `npm run build` (para registrar `reportes.css` en el manifiesto de Vite).
4. `vendor/bin/pint --dirty --format agent` (formato de código).

> Si otro entorno clona el proyecto, basta con `composer install` y `npm run build`.

---

## 11. Cómo usarlo

1. Iniciar sesión con un usuario **admin**, **propietario** o **fiscalizador**.
2. En el menú lateral → **Reportes** → elegir uno de los tres reportes.
3. Aplicar los filtros y presionar **Filtrar**.
4. Usar **Imprimir**, **Exportar PDF** o **Exportar Excel** según se necesite.

---

## 12. Pendientes / mejoras opcionales

- Registrar automáticamente el estado `omitido` en `ControlRecorridoService` cuando la unidad avance de parada sin pasar por la anterior (hoy ese estado no se escribe; el reporte lo deriva).
- Habilitar `ext-zip` y `maatwebsite/excel` para exportación `.xlsx` nativa con formato.
