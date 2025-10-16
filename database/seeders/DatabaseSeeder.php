<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bloque;
use App\Models\Pieza;
use App\Models\Proyecto;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $bicm = Proyecto::create(['nombre' => 'BICM']);
        $balc = Proyecto::create(['nombre' => 'BALC']);
        $opv  = Proyecto::create(['nombre' => 'OPV']);
        Proyecto::create(['nombre' => 'BRF']);

        $b_bicm_1110 = Bloque::create(['proyecto_id'=>$bicm->id,'nombre'=>'1110']);
        $b_balc_2210 = Bloque::create(['proyecto_id'=>$balc->id,'nombre'=>'2210']);
        $b_bicm_3510 = Bloque::create(['proyecto_id'=>$bicm->id,'nombre'=>'3510']);
        $b_bicm_3610 = Bloque::create(['proyecto_id'=>$bicm->id,'nombre'=>'3610']);
        $b_balc_3310 = Bloque::create(['proyecto_id'=>$balc->id,'nombre'=>'3310']);
        $b_opv_2210  = Bloque::create(['proyecto_id'=>$opv->id,'nombre'=>'2210']);

        Pieza::insert([
            ['bloque_id'=>$b_balc_2210->id,'codigo'=>'B01','nombre'=>'Pieza B01','peso_teorico'=>4.000,'estado'=>'Pendiente','created_at'=>now(),'updated_at'=>now()],
            ['bloque_id'=>$b_balc_3310->id,'codigo'=>'A02','nombre'=>'Pieza A02','peso_teorico'=>20.000,'estado'=>'Pendiente','created_at'=>now(),'updated_at'=>now()],
            ['bloque_id'=>$b_opv_2210->id, 'codigo'=>'H12','nombre'=>'Pieza H12','peso_teorico'=>16.000,'estado'=>'Pendiente','created_at'=>now(),'updated_at'=>now()],
            ['bloque_id'=>$b_bicm_1110->id,'codigo'=>'R23','nombre'=>'Pieza R23','peso_teorico'=>null,'estado'=>'Pendiente','created_at'=>now(),'updated_at'=>now()],
        ]);
    }
}