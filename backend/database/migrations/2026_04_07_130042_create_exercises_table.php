<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('exercises', function (Blueprint $table) {
        $table->id();
        
        $table->string('name');
        // Nombre del ejercicio: "Pull-up", "Dip", "Pistol Squat"
        
        $table->string('muscle_group');
        // Grupo muscular principal: "back", "chest", "legs", "core"
        
        $table->enum('category', ['push', 'pull', 'legs', 'core', 'full_body']);
        // Categoría de movimiento — útil para filtrar y analizar por patrón
        
        $table->enum('load_type', ['bodyweight', 'weighted', 'assisted']);
        // bodyweight = solo peso corporal
        // weighted   = añades lastre (chaleco, mancuerna)
        // assisted   = banda elástica que resta resistencia
        
        $table->boolean('is_default')->default(true);
        // true  = ejercicio del sistema, visible para todos
        // false = creado por un usuario concreto
        
        $table->foreignId('user_id')
              ->nullable()
              ->constrained()
              ->nullOnDelete();
        // nullable() porque los ejercicios del sistema no pertenecen a nadie
        // nullOnDelete() → si el usuario se borra, el ejercicio no se borra,
        //                  simplemente pierde su user_id (pasa a null)
        
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('exercises');
}
};
