<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Proyecto extends Model
{
    use HasFactory;

    protected $table = 'proyectos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'estado',
    ];

    /** Relaciones */
    public function bloques(): HasMany
    {
        return $this->hasMany(Bloque::class);
    }

    public function piezas(): HasManyThrough
    {
        return $this->hasManyThrough(Pieza::class, Bloque::class, 'proyecto_id', 'bloque_id', 'id', 'id');
    }

    /** Scopes */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
