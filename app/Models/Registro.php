<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    use HasFactory;

    protected $table = 'registros';

    protected $fillable = [
        'pieza_id',
        'peso_real',
        'diferencia_peso',
        'user_id'
    ];

    public function pieza()
    {
        return $this->belongsTo(Pieza::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
