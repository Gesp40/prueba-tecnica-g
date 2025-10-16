<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyecto;

class ProyectosSeeder extends Seeder
{
    public function run(): void
    {
        $proyectos = [
            ['nombre' => 'BICM', 'descripcion' => 'Oceanográfico', 'estado' => 'activo'],
            ['nombre' => 'BALC', 'descripcion' => 'Buque DA',      'estado' => 'activo'],
            ['nombre' => 'OPV',  'descripcion' => 'Offshore',      'estado' => 'activo'],
            ['nombre' => 'BRF',  'descripcion' => 'Recfluvial',    'estado' => 'activo'],
        ];

        foreach ($proyectos as $p) {
            Proyecto::updateOrCreate(
                ['nombre' => $p['nombre']],
                ['descripcion' => $p['descripcion'], 'estado' => $p['estado']]
            );
        }
    }
}
