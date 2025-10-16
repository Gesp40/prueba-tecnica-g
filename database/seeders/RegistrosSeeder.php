<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Pieza;
use App\Models\Registro;

class RegistrosSeeder extends Seeder
{
    public function run(): void
    {
        // Mapeo "quién registró" según tus tablas de referencia
        $quien = [
            'B01' => 'Gabriel',
            'H12' => 'Sergio',
            // Si luego agregas más fabricadas, extiende aquí
        ];

        foreach ($quien as $codigoPieza => $nombreUsuario) {
            $pieza = Pieza::where('codigo', $codigoPieza)->where('estado', 'fabricada')->first();
            $user  = User::where('name', $nombreUsuario)->first();

            if ($pieza && $user) {
                $pesoT = (float) $pieza->peso_teorico;
                $pesoR = $pesoT; // temporal (luego lo ajustas desde el formulario)

                Registro::updateOrCreate(
                    ['pieza_id' => $pieza->id, 'user_id' => $user->id, 'registrado_en' => Carbon::now()],
                    [
                        'peso_teorico' => $pesoT,
                        'peso_real'    => $pesoR,
                        'diferencia'   => $pesoT - $pesoR, // 0 por ahora
                    ]
                );
            }
        }
    }
}
