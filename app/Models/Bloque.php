<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bloque extends Model
{
    use HasFactory;

    protected $table = 'bloques';

    protected $fillable = [
        'nombre',
        'proyecto_id',
        'descripcion',
        'estado',
    ];

    /** Relaciones */
    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function piezas(): HasMany
    {
        return $this->hasMany(Pieza::class);
    }

    /** Scopes */
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
