# Informe de cambios, pruebas y pendientes — Línea 61

**Fecha:** 2 de noviembre de 2026  
**Proyectos revisados:** Laravel web, Flutter móvil y ApiDart.

## Resumen

Laravel concentra las reglas de asignación, GPS y control de recorrido. La app móvil inicia sesión en Laravel y envía sus operaciones transaccionales a ApiDart; ApiDart reenvía la solicitud y el token Bearer a Laravel. Así, web y móvil usan las mismas reglas y no mantienen dos cálculos distintos del recorrido.

En este dominio, “asignación”, “seguimiento GPS” y “control de recorrido” son tres flujos operativos. La recepción de un punto GPS y su evaluación se guardan en una transacción de base de datos. El inicio del turno también quedó agrupado con la planificación de paradas.

## Cambios realizados

### Laravel: permisos, correo y transacciones

- Las rutas de negocio requieren correo verificado. Las rutas del conductor requieren además el rol `conductor`; los controladores siguen comprobando que el conductor sea dueño de la asignación. Las operaciones administrativas usan roles autorizados.
- El inicio de un turno usa `DB::transaction` y bloquea la fila de asignación. El cambio de estado y la creación del itinerario se confirman juntos o se revierten juntos. Ver `app/Services/AsignacionTurnoService.php` y `routes/api.php`.
- El servicio GPS guarda el punto y evalúa el control de recorrido dentro de una transacción. La regla existente considera una parada cumplida dentro de 80 m; si se detecta una posterior, las intermedias se registran como omitidas. Ver `app/Services/SeguimientoGpsService.php` y `app/Services/ControlRecorridoService.php`.
- La API permite consultar el estado de verificación y reenviar el enlace. La app no dispone de una ruta para marcar por sí misma un correo como verificado. El registro web dispara la notificación de Laravel.
- Existe una opción de configuración para omitir la verificación: `AUTO_VERIFY_EMAIL` al registrar y `AUTO_VERIFY_ON_LOGIN` al iniciar sesión o consultar el perfil. Ambas opciones tienen valor predeterminado `false` en `config/linea61.php`. Si se activan, las cuentas quedan verificadas automáticamente y el correo deja de ser una barrera efectiva.
- Se añadieron pruebas para el rechazo de usuarios sin rol de conductor y para consultar el recorrido propio desde móvil.

### Flutter móvil

- El login y la consulta del perfil usan Laravel. Asignaciones, inicio y fin de turno, GPS, sincronización y consulta del recorrido usan ApiDart con el mismo token de Sanctum.
- El login detecta cuentas sin verificar y ofrece reenviar el enlace.
- La pantalla del turno activo usa ubicación real, reporta cada 10 segundos mientras la pantalla está activa y muestra los estados de paradas devueltos por Laravel.
- El manifiesto de **debug** permite HTTP para la red local. No se habilitó HTTP en el manifiesto principal de release. Ver `android/app/src/debug/AndroidManifest.xml`.

### ApiDart

- Se agregó un proxy que reenvía método, ruta, query, cuerpo y token Bearer a Laravel. Las rutas transaccionales móviles usan ese proxy.
- Se reordenaron las rutas: el montaje amplio de `/api` podía interceptar los catálogos antes de que llegaran a sus controladores MySQL.
- Se añadió una prueba del reenvío a Laravel y otra que comprueba que el catálogo de conductores responda.

## Pruebas ejecutadas

- Laravel: **116 pasaron**, **1 omitida**, 411 aserciones.
- ApiDart: **6 pruebas pasaron** y `dart analyze` no encontró incidencias.
- Flutter: pasó la prueba de inicio de interfaz; los archivos modificados del flujo transaccional pasaron el análisis dirigido. Se generó el APK debug.
- Vite: `npm run build` finalizó correctamente.
- El análisis global de Flutter todavía informa **19 avisos informativos** en otras pantallas y formularios. No son errores de compilación.

No se hizo una prueba GPS de extremo a extremo con un teléfono. `flutter devices` detectó Windows y navegadores, pero no un teléfono ni emulador. El APK compilado usa las URLs predeterminadas del emulador; hay que compilar o ejecutar Flutter con los `--dart-define` de la sección siguiente para usar un teléfono físico.

## Correcciones pendientes y preparación para probar

### Prioridad alta: probar desde el teléfono

1. Conecta teléfono y PC a la misma Wi‑Fi. Usa `ipconfig` en Windows para obtener la IPv4 del PC.
2. Inicia Laravel en el PC con `php artisan serve --host=0.0.0.0 --port=8001`.
3. Inicia ApiDart en el PC en el puerto 8000. Configura `LARAVEL_API_URL=http://127.0.0.1:8001/api` y los valores `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD` para que coincidan con la base local.
4. Permite los puertos TCP 8000 y 8001 en el Firewall de Windows para una red privada. Desde el teléfono, comprueba `http://IP_DEL_PC:8000/` y `http://IP_DEL_PC:8001/login`.
5. Ejecuta Flutter con la IP real del PC:

   ```powershell
   flutter run -d ID_DEL_TELEFONO --dart-define=API_BASE_URL=http://IP_DEL_PC:8001/api --dart-define=DART_API_BASE_URL=http://IP_DEL_PC:8000/api
   ```

   El teléfono debe tener depuración USB habilitada y autorizada si se usa `flutter run`. Para instalar por APK, primero compila con esos mismos `--dart-define`.

### Prioridad alta: cuenta, rol y correo

- La app móvil **no tiene pantalla ni endpoint de registro de usuarios**. Para las pruebas, crea la cuenta desde web o administración. Asigna el rol `conductor`, vincula su ficha de conductor y crea una asignación activa para el día.
- Revisa localmente `AUTO_VERIFY_EMAIL` y `AUTO_VERIFY_ON_LOGIN` en `.env`. Para exigir verificación real, ambos deben ser `false`; después ejecuta `php artisan config:clear`. No se leyó ni se copió ningún valor de `.env` al preparar este informe.
- `.env.example` usa `MAIL_MAILER=log`; con ese valor el enlace se escribe en el log y no se envía a una bandeja. Para probar entrega real configura SMTP y establece `APP_URL` a una dirección que el teléfono pueda abrir. La verificación de entrega SMTP no se probó.

### Prioridad alta antes de exponer ApiDart

- Los endpoints CRUD directos de catálogos en ApiDart escriben en MySQL sin aplicar los roles de Laravel, y el servidor permite CORS `*`. Aunque los flujos de asignación y GPS pasan por Laravel, restringe esos catálogos a una red privada o añade autenticación y autorización antes de publicar ApiDart.
- La app no tiene una cola offline integrada ni servicio de ubicación en segundo plano. Para la demostración mantén la pantalla del turno activa, la ubicación precisa habilitada y conexión disponible. Para producción, implementa almacenamiento local y una estrategia de ubicación en segundo plano.
- Para completar el control de recorrido, el GPS debe estar a menos de 80 m de las coordenadas configuradas de una parada. Si se prueba desde otro lugar, los puntos pueden guardarse correctamente y las paradas seguir pendientes.
- Usa HTTPS y URLs públicas en producción. La excepción HTTP del manifiesto solo corresponde a debug en la red local.

## Estado del repositorio

El estado de Git incluye archivos modificados y documentos eliminados que no se deben atribuir automáticamente a este informe. Antes de confirmar cambios, revisa `git status` y decide si las eliminaciones de `AGENTS.md`, `CLAUDE.md`, `INFORME_CAMBIOS_UIUX_LINEA61.md`, `NORMALIZACION_USUARIOS_PERFILES.md` y los documentos DOCX son intencionales. También hay cambios locales en Flutter y ApiDart que conviene revisar antes de hacer commit.
