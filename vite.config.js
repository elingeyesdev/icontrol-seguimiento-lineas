import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/guest.css', 'resources/css/dashboard.css', 'resources/css/monitoreo.css', 'resources/css/control-recorrido.css', 'resources/css/catalogos.css', 'resources/css/errors.css', 'resources/css/reportes.css', 'resources/js/app.js', 'resources/js/catalogos.js', 'resources/js/roles.js', 'resources/js/dashboard.js', 'resources/js/monitoreo.js', 'resources/js/ruta-form.js', 'resources/js/parada-map.js'],
            refresh: true,
        }),
    ],
});
