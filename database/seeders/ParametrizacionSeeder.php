<?php

namespace Database\Seeders;

use App\Models\Conductor;
use App\Models\Interno;
use App\Models\Micro;
use App\Models\Propietario;
use Illuminate\Database\Seeder;

class ParametrizacionSeeder extends Seeder
{
    /**
     * Crea registros de prueba para los catálogos de transporte.
     */
    public function run(): void
    {
        $propietarios = [
            ['nombre' => 'Juan', 'apellido' => 'Pérez', 'telefono' => '70010001', 'correo' => 'juan.perez@linea61.test', 'ci' => '8100001', 'estado' => 'activo', 'fecha_registro' => '2026-01-10'],
            ['nombre' => 'María', 'apellido' => 'López', 'telefono' => '70010002', 'correo' => 'maria.lopez@linea61.test', 'ci' => '8100002', 'estado' => 'activo', 'fecha_registro' => '2026-01-11'],
            ['nombre' => 'Carlos', 'apellido' => 'Gómez', 'telefono' => '70010003', 'correo' => 'carlos.gomez@linea61.test', 'ci' => '8100003', 'estado' => 'activo', 'fecha_registro' => '2026-01-12'],
            ['nombre' => 'Ana', 'apellido' => 'Rojas', 'telefono' => '70010004', 'correo' => 'ana.rojas@linea61.test', 'ci' => '8100004', 'estado' => 'activo', 'fecha_registro' => '2026-01-13'],
            ['nombre' => 'Luis', 'apellido' => 'Vargas', 'telefono' => '70010005', 'correo' => 'luis.vargas@linea61.test', 'ci' => '8100005', 'estado' => 'activo', 'fecha_registro' => '2026-01-14'],
        ];

        foreach ($propietarios as $propietario) {
            Propietario::updateOrCreate(['correo' => $propietario['correo']], $propietario);
        }

        $conductores = [
            ['nombre' => 'Miguel', 'apellido' => 'Quispe', 'telefono' => '70110001', 'correo' => 'miguel.quispe@linea61.test', 'ci' => '8200001', 'estado' => 'activo'],
            ['nombre' => 'Sofía', 'apellido' => 'Mamani', 'telefono' => '70110002', 'correo' => 'sofia.mamani@linea61.test', 'ci' => '8200002', 'estado' => 'activo'],
            ['nombre' => 'Diego', 'apellido' => 'Flores', 'telefono' => '70110003', 'correo' => 'diego.flores@linea61.test', 'ci' => '8200003', 'estado' => 'activo'],
            ['nombre' => 'Paola', 'apellido' => 'Rivera', 'telefono' => '70110004', 'correo' => 'paola.rivera@linea61.test', 'ci' => '8200004', 'estado' => 'activo'],
            ['nombre' => 'Jorge', 'apellido' => 'Mendoza', 'telefono' => '70110005', 'correo' => 'jorge.mendoza@linea61.test', 'ci' => '8200005', 'estado' => 'activo'],
        ];

        foreach ($conductores as $conductor) {
            Conductor::updateOrCreate(['correo' => $conductor['correo']], $conductor);
        }

        $internos = [
            ['numero_interno' => 'INT-101', 'estado' => 'asignado', 'fecha_ingreso' => '2024-01-15 08:00:00', 'observaciones' => 'Unidad asignada a la ruta Centro.'],
            ['numero_interno' => 'INT-102', 'estado' => 'asignado', 'fecha_ingreso' => '2024-02-20 08:00:00', 'observaciones' => 'Unidad asignada a la ruta Norte.'],
            ['numero_interno' => 'INT-103', 'estado' => 'asignado', 'fecha_ingreso' => '2024-03-10 08:00:00', 'observaciones' => 'Unidad asignada a la ruta Sur.'],
            ['numero_interno' => 'INT-104', 'estado' => 'asignado', 'fecha_ingreso' => '2024-04-05 08:00:00', 'observaciones' => 'Unidad asignada a la ruta Oeste.'],
            ['numero_interno' => 'INT-105', 'estado' => 'asignado', 'fecha_ingreso' => '2024-05-12 08:00:00', 'observaciones' => 'Unidad asignada a la ruta Este.'],
        ];

        foreach ($internos as $interno) {
            Interno::updateOrCreate(['numero_interno' => $interno['numero_interno']], $interno);
        }

        $propietarioIds = Propietario::whereIn('correo', array_column($propietarios, 'correo'))->pluck('id', 'correo')->all();
        $internoIds = Interno::whereIn('numero_interno', array_column($internos, 'numero_interno'))->pluck('id', 'numero_interno')->all();

        $micros = [
            ['placa' => '6101-ABC', 'chasis' => 'CHS-L61-0001', 'anio_fabricacion' => 2019, 'modelo' => 'Coaster', 'marca' => 'Toyota', 'capacidad_pasajeros' => 30],
            ['placa' => '6102-ABC', 'chasis' => 'CHS-L61-0002', 'anio_fabricacion' => 2020, 'modelo' => 'County', 'marca' => 'Hyundai', 'capacidad_pasajeros' => 32],
            ['placa' => '6103-ABC', 'chasis' => 'CHS-L61-0003', 'anio_fabricacion' => 2021, 'modelo' => 'Busscar', 'marca' => 'Mercedes-Benz', 'capacidad_pasajeros' => 34],
            ['placa' => '6104-ABC', 'chasis' => 'CHS-L61-0004', 'anio_fabricacion' => 2018, 'modelo' => 'Volare', 'marca' => 'Agrale', 'capacidad_pasajeros' => 28],
            ['placa' => '6105-ABC', 'chasis' => 'CHS-L61-0005', 'anio_fabricacion' => 2022, 'modelo' => 'Marcopolo', 'marca' => 'Volkswagen', 'capacidad_pasajeros' => 35],
        ];

        foreach ($micros as $index => $micro) {
            Micro::updateOrCreate(['placa' => $micro['placa']], [
                ...$micro,
                'propietario_id' => $propietarioIds[$propietarios[$index]['correo']],
                'interno_id' => $internoIds[$internos[$index]['numero_interno']],
                'estado' => 'activo',
            ]);
        }

    }
}
