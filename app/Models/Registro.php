<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Registro extends Model
{
    use HasFactory;

    protected $table = 'registros';

    protected $fillable = [
        'pieza_id',
        'user_id',
        'registrado_en',
        'peso_teorico',
        'peso_real',
        'diferencia',
    ];

    protected $casts = [
        'registrado_en' => 'datetime',
        'peso_teorico'  => 'decimal:3',
        'peso_real'     => 'decimal:3',
        'diferencia'    => 'decimal:3',
    ];

    /** Relaciones */
    public function pieza(): BelongsTo
    {
        return $this->belongsTo(Pieza::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $registro) {
            if (is_null($registro->diferencia) && !is_null($registro->peso_teorico) && !is_null($registro->peso_real)) {
                $registro->diferencia = $registro->peso_teorico - $registro->peso_real;
            }
        });
    }
}
