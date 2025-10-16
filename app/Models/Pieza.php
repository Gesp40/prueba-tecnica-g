<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pieza extends Model
{
    use HasFactory;

    protected $table = 'piezas';

    protected $fillable = [
        'nombre',
        'bloque_id',
        'peso_teorico',
        'estado'
    ];

    public function bloque()
    {
        return $this->belongsTo(Bloque::class);
    }

    public function registros()
    {
        return $this->hasMany(Registro::class);
    }
}
