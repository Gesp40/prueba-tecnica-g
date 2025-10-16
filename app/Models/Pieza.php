<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pieza extends Model
{
    use HasFactory;

    protected $table = 'piezas';

    protected $fillable = [
        'codigo',        // <- agregado para permitir mass assignment (seeders, formularios)
        'nombre',
        'bloque_id',
        'peso_teorico',
        'estado',
    ];

    protected $casts = [
        'peso_teorico' => 'decimal:3',
    ];

    /** Relaciones */
    public function bloque(): BelongsTo
    {
        return $this->belongsTo(Bloque::class);
    }

    public function registros(): HasMany
    {
        return $this->hasMany(Registro::class);
    }

    /** Scopes */
    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeFabricada($query)
    {
        return $query->where('estado', 'fabricada');
    }
}
