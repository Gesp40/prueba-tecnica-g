<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bloques', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->foreignId('proyecto_id')->constrained('proyectos')->cascadeOnDelete();
            $table->string('descripcion')->nullable();
            $table->string('estado')->default('activo');
            $table->timestamps();

            // Evita duplicado de nombre de bloque dentro de un mismo proyecto
            $table->unique(['proyecto_id', 'nombre']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bloques');
    }
};
