<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bloque;
use App\Models\Pieza;

class PiezasSeeder extends Seeder
{
    public function run(): void
    {
        $bloquesByProyecto = Bloque::get()
            ->groupBy(fn($b) => $b->proyecto->nombre)
            ->map(fn($group) => $group->pluck('id', 'nombre')->toArray())
            ->toArray();

        $rows = [
            // codigo, proyecto, bloque_nombre, peso_teorico, estado
            ['B01', 'BALC', '2210',  4.000, 'fabricada'],
            ['A02', 'BALC', '3310', 20.000, 'pendiente'],
            ['H12', 'OPV',  '2210', 16.000, 'fabricada'],
            ['R23', 'BICM', '1110', 13.000, 'pendiente'],
        ];

        foreach ($rows as [$codigo, $proyecto, $bloqueNombre, $peso, $estado]) {
            $bloqueId = $bloquesByProyecto[$proyecto][$bloqueNombre] ?? null;
            if (!$bloqueId) {
                continue;
            }

            Pieza::updateOrCreate(
                ['bloque_id' => $bloqueId, 'codigo' => $codigo],
                [
                    'nombre'       => 'Pieza ' . $codigo,
                    'peso_teorico' => $peso,
                    'estado'       => $estado,
                ]
            );
        }
    }
}
