<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bloque extends Model
{
    use HasFactory;

    protected $table = 'bloques';

    protected $fillable = [
        'nombre',
        'proyecto_id',
        'descripcion',
        'estado'
    ];

    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function piezas()
    {
        return $this->hasMany(Pieza::class);
    }
}
