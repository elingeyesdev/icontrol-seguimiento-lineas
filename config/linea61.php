<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Verificación de correo
    |--------------------------------------------------------------------------
    |
    | La app móvil no debe depender de abrir el enlace enviado por correo.
    | Con AUTO_VERIFY_EMAIL=true la cuenta queda verificada al registrarse y
    | con AUTO_VERIFY_ON_LOGIN=true se verifica al iniciar sesión o al
    | consultar el perfil con un token existente. El enlace firmado sigue
    | disponible como respaldo cuando ambas opciones están en false.
    |
    */
    'auto_verify_email' => (bool) env('AUTO_VERIFY_EMAIL', false),

    'auto_verify_on_login' => (bool) env('AUTO_VERIFY_ON_LOGIN', false),
];
