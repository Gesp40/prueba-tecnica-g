<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pieza_id')->constrained('piezas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Fecha/hora automática de creación del registro
            $table->timestamp('registrado_en')->useCurrent();

            // Guardamos valores usados en el momento del registro
            $table->decimal('peso_teorico', 10, 3)->default(0);
            $table->decimal('peso_real', 10, 3);
            $table->decimal('diferencia', 10, 3)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros');
    }
};
