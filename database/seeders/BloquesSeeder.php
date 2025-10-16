<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proyecto;
use App\Models\Bloque;

class BloquesSeeder extends Seeder
{
    public function run(): void
    {
        $proy = Proyecto::pluck('id', 'nombre'); // ['BICM' => 1, 'BALC' => 2, ...]

        $bloques = [
            ['proyecto' => 'BICM', 'nombre' => '1110'],
            ['proyecto' => 'BALC', 'nombre' => '2210'],
            ['proyecto' => 'BICM', 'nombre' => '3510'],
            ['proyecto' => 'BICM', 'nombre' => '3610'],
            ['proyecto' => 'BALC', 'nombre' => '3310'],
            ['proyecto' => 'OPV',  'nombre' => '2210'],
        ];

        foreach ($bloques as $b) {
            $proyectoId = $proy[$b['proyecto']] ?? null;
            if ($proyectoId) {
                Bloque::updateOrCreate(
                    ['proyecto_id' => $proyectoId, 'nombre' => $b['nombre']],
                    ['descripcion' => null, 'estado' => 'activo']
                );
            }
        }
    }
}
